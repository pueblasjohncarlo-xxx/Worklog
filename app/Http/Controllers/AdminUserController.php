<?php

namespace App\Http\Controllers;

use App\Http\Requests\Admin\CreateUserRequest;
use App\Http\Requests\Admin\UpdateUserRoleRequest;
use App\Models\AuditLog;
use App\Models\Company;
use App\Models\SupervisorProfile;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminUserController extends Controller
{
    /**
     * Constructor - Authorize all admin user operations
     */
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            // Verify user is authenticated and has admin/staff role
            $allowedRoles = [User::ROLE_ADMIN, User::ROLE_STAFF];
            if (!Auth::check() || !in_array((string) Auth::user()->role, $allowedRoles, true)) {
                Log::warning('Unauthorized access attempt to admin user management', [
                    'user_id' => Auth::id(),
                    'ip' => $request->ip(),
                    'route' => $request->path(),
                ]);
                throw new AuthorizationException('Unauthorized: User management is restricted to administrators only.');
            }
            return $next($request);
        });
    }

    public function index(): View
    {
        $this->authorize('viewAny', User::class);

        $users = User::orderBy('name')->get();
        $companies = Company::orderBy('name')->get();

        // Group students by section (or 'No Section' if null)
        $students = $users->where('role', 'student')->groupBy(function ($user) {
            return $user->section ? $user->section : 'No Section';
        })->sortKeys();

        // Group other roles
        $admins = $users->where('role', 'admin');
        $staff = $users->where('role', 'staff');
        $coordinators = $users->where('role', 'coordinator');
        $supervisors = $users->where('role', 'supervisor');
        $ojtAdvisers = $users->where('role', 'ojt_adviser');

        return view('admin.users.index', [
            'studentsBySection' => $students,
            'admins' => $admins,
            'staff' => $staff,
            'coordinators' => $coordinators,
            'supervisors' => $supervisors,
            'ojtAdvisers' => $ojtAdvisers,
            'allUsers' => $users,
            'companies' => $companies,
        ]);
    }

    public function store(CreateUserRequest $request): RedirectResponse
    {
        $this->authorize('create', User::class);

        $data = $request->validated();
        $adminUser = Auth::user();

        try {
            // Check if user exists
            $user = User::where('email', $data['email'])->first();

            if ($user) {
                // Update existing user
                $oldRole = $user->role;
                $oldApprovalStatus = $user->is_approved;

                $user->update([
                    'name' => $data['name'],
                    'password' => Hash::make($data['password']),
                    'encrypted_password' => Crypt::encryptString($data['password']),
                    'role' => $data['role'],
                    'is_approved' => true,
                    'has_requested_account' => true,
                ]);

                if ($data['role'] === User::ROLE_OJT_ADVISER) {
                    $user->ojtAdviserProfile()->updateOrCreate(
                        ['user_id' => $user->id],
                        [
                            'department' => $data['department'] ?? null,
                            'phone' => $data['phone'] ?? null,
                            'address' => $data['address'] ?? null,
                        ]
                    );
                }

                if ($data['role'] === User::ROLE_SUPERVISOR) {
                    SupervisorProfile::updateOrCreate(
                        ['user_id' => $user->id],
                        [
                            'company_id' => (int) $data['company_id'],
                            'department' => $data['department'] ?? null,
                            'phone' => $data['phone'] ?? null,
                        ]
                    );
                }

                // Log user update action
                $this->logAuditAction('user_updated', $user, [
                    'old_role' => $oldRole,
                    'new_role' => $data['role'],
                    'old_approval_status' => $oldApprovalStatus,
                    'new_approval_status' => true,
                    'updated_by_admin' => $adminUser->name,
                ]);

                $message = 'User account activated and updated.';
            } else {
                // Create new user
                $newUser = User::create([
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'password' => Hash::make($data['password']),
                    'encrypted_password' => Crypt::encryptString($data['password']),
                    'role' => $data['role'],
                    'is_approved' => true,
                    'has_requested_account' => true,
                ]);

                if ($data['role'] === User::ROLE_OJT_ADVISER) {
                    $newUser->ojtAdviserProfile()->create([
                        'department' => $data['department'] ?? null,
                        'phone' => $data['phone'] ?? null,
                        'address' => $data['address'] ?? null,
                    ]);
                }

                if ($data['role'] === User::ROLE_SUPERVISOR) {
                    SupervisorProfile::create([
                        'user_id' => $newUser->id,
                        'company_id' => (int) $data['company_id'],
                        'department' => $data['department'] ?? null,
                        'phone' => $data['phone'] ?? null,
                    ]);
                }

                // Log user creation action
                $this->logAuditAction('user_created', $newUser, [
                    'email' => $data['email'],
                    'role' => $data['role'],
                    'created_by_admin' => $adminUser->name,
                    'ip_address' => $request->ip(),
                ]);

                $message = 'User created successfully.';
            }

            return redirect()->route('admin.users.index')
                ->with('status', $message);
        } catch (\Exception $e) {
            Log::error('Error creating/updating user', [
                'admin_id' => $adminUser->id,
                'email' => $data['email'],
                'error' => $e->getMessage(),
            ]);

            return redirect()->back()
                ->withErrors(['error' => 'An error occurred while processing the user.']);
        }
    }

    public function updateRole(UpdateUserRoleRequest $request, User $user): RedirectResponse
    {
        $this->authorize('changeRole', $user);

        $adminUser = Auth::user();
        $oldRole = $user->role;
        $newRole = $request->validated()['role'];

        try {
            $user->update([
                'role' => $newRole,
            ]);

            // Log role change action
            $this->logAuditAction('user_role_changed', $user, [
                'old_role' => $oldRole,
                'new_role' => $newRole,
                'changed_by_admin' => $adminUser->name,
                'ip_address' => $request->ip(),
            ]);

            return redirect()->route('admin.users.index')
                ->with('status', 'Role updated successfully. User role changed from ' . $oldRole . ' to ' . $newRole);
        } catch (\Exception $e) {
            Log::error('Error updating user role', [
                'admin_id' => $adminUser->id,
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            return redirect()->back()
                ->withErrors(['error' => 'An error occurred while updating the role.']);
        }
    }

    public function show(User $user): View
    {
        $this->authorize('view', $user);

        return view('admin.users.show', [
            'user' => $user,
        ]);
    }

    public function destroy(User $user): RedirectResponse
    {
        $this->authorize('delete', $user);

        $adminUser = Auth::user();

        try {
            $userEmail = $user->email;
            $userId = $user->id;

            $user->delete();

            // Log user deletion
            $this->logAuditAction('user_deleted', null, [
                'user_id' => $userId,
                'email' => $userEmail,
                'deleted_by_admin' => $adminUser->name,
                'ip_address' => request()->ip(),
            ]);

            return redirect()->route('admin.users.index')
                ->with('status', 'User deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Error deleting user', [
                'admin_id' => $adminUser->id,
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            return redirect()->back()
                ->withErrors(['error' => 'An error occurred while deleting the user.']);
        }
    }

    public function pending(): View
    {
        return redirect()->route('admin.users.index')
            ->with('status', 'Pending approvals are disabled. Accounts are handled through direct user management.');
    }

    public function bulkAction(Request $request): RedirectResponse
    {
        return redirect()->route('admin.users.index')
            ->with('status', 'Pending approval bulk actions are disabled.');
    }

    public function approve(User $user): RedirectResponse
    {
        return redirect()->route('admin.users.index')
            ->with('status', 'Manual approval is disabled.');
    }

    public function reject(User $user): RedirectResponse
    {
        return redirect()->route('admin.users.index')
            ->with('status', 'Manual approval rejection is disabled.');
    }

    public function resetPassword(Request $request, User $user): RedirectResponse
    {
        $this->authorize('resetPassword', $user);

        $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $adminUser = Auth::user();

        try {
            $user->update([
                'password' => Hash::make($request->password),
                'encrypted_password' => Crypt::encryptString($request->password),
            ]);

            // Log password reset action
            $this->logAuditAction('user_password_reset', $user, [
                'reset_by_admin' => $adminUser->name,
                'ip_address' => $request->ip(),
            ]);

            return redirect()->back()->with('status', 'Password reset successfully.');
        } catch (\Exception $e) {
            Log::error('Error resetting user password', [
                'admin_id' => $adminUser->id,
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            return redirect()->back()
                ->withErrors(['error' => 'An error occurred while resetting the password.']);
        }
    }

    // Export a CSV list of student login usernames (emails) and status
    public function exportStudents(Request $request): StreamedResponse
    {
        $this->authorize('viewAny', User::class);

        $students = User::where('role', User::ROLE_STUDENT)->orderBy('name')->get();

        // Log export action
        $this->logAuditAction('student_list_exported', null, [
            'exported_by_admin' => Auth::user()->name,
            'student_count' => count($students),
            'ip_address' => $request->ip(),
        ]);

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="student_logins.csv"',
        ];

        return response()->stream(function () use ($students) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Name', 'Email (Username)', 'Status', 'Default Password Hint']);
            foreach ($students as $s) {
                $status = $s->is_approved ? 'Active' : ($s->has_requested_account ? 'Pending' : 'Imported');
                $hint = (!$s->has_requested_account && $s->role === User::ROLE_STUDENT)
                    ? strtolower(last(explode(' ', $s->name))) . '123'
                    : '';
                fputcsv($out, [$s->name, $s->email, $status, $hint]);
            }
            fclose($out);
        }, 200, $headers);
    }

    /**
     * Log audit action for user management operations.
     * CRITICAL: This provides detailed tracking of all admin actions on users.
     *
     * @param string $action The action being performed
     * @param User|null $user The user being acted upon
     * @param array $details Additional context details
     */
    private function logAuditAction(string $action, ?User $user, array $details = []): void
    {
        try {
            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => 'admin_' . $action,
                'auditable_type' => User::class,
                'auditable_id' => $user?->id,
                'old_values' => null,
                'new_values' => json_encode($details),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to log audit action', [
                'action' => $action,
                'user_id' => $user?->id,
                'admin_id' => Auth::id(),
                'error' => $e->getMessage(),
            ]);
        }
    }
}

