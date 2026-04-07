<?php

namespace App\Http\Controllers;

use App\Http\Requests\Admin\CreateUserRequest;
use App\Http\Requests\Admin\UpdateUserRoleRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash; // Add this line
use Illuminate\View\View; // Add this line
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminUserController extends Controller
{
    public function index(): View
    {
        $users = User::orderBy('name')->get();

        // Group students by section (or 'No Section' if null)
        $students = $users->where('role', 'student')->groupBy(function ($user) {
            return $user->section ? $user->section : 'No Section';
        })->sortKeys();

        // Group other roles
        $admins = $users->where('role', 'admin');
        $coordinators = $users->where('role', 'coordinator');
        $supervisors = $users->where('role', 'supervisor');
        $ojtAdvisers = $users->where('role', 'ojt_adviser');

        return view('admin.users.index', [
            'studentsBySection' => $students,
            'admins' => $admins,
            'coordinators' => $coordinators,
            'supervisors' => $supervisors,
            'ojtAdvisers' => $ojtAdvisers,
            'allUsers' => $users, // Keep this if needed for other parts
        ]);
    }

    public function store(CreateUserRequest $request): RedirectResponse
    {
        $data = $request->validated();

        // Check if user exists
        $user = User::where('email', $data['email'])->first();

        if ($user) {
            // Update existing user
            $user->update([
                'name' => $data['name'],
                'password' => Hash::make($data['password']),
                'encrypted_password' => Crypt::encryptString($data['password']), // Store encrypted
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

            $message = 'User account activated and updated.';
        } else {
            // Create new user
            $newUser = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'encrypted_password' => Crypt::encryptString($data['password']), // Store encrypted
                'role' => $data['role'],
                'is_approved' => true, // Auto-approve users created by Admin
                'has_requested_account' => true, // Mark as active account
            ]);

            if ($data['role'] === User::ROLE_OJT_ADVISER) {
                $newUser->ojtAdviserProfile()->create([
                    'department' => $data['department'] ?? null,
                    'phone' => $data['phone'] ?? null,
                    'address' => $data['address'] ?? null,
                ]);
            }

            $message = 'User created successfully.';
        }

        return redirect()->route('admin.users.index')
            ->with('status', $message);
    }

    public function updateRole(UpdateUserRoleRequest $request, User $user): RedirectResponse
    {
        $user->update([
            'role' => $request->validated()['role'],
        ]);

        return redirect()->route('admin.users.index')
            ->with('status', 'user-role-updated');
    }

    public function show(User $user): View
    {
        return view('admin.users.show', [
            'user' => $user,
        ]);
    }

    public function destroy(User $user): RedirectResponse
    {
        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('status', 'user-deleted');
    }

    public function pending(): View
    {
        $users = User::where('is_approved', false)
            ->where('has_requested_account', true) // Only show those who actively requested
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.users.pending', [
            'users' => $users,
        ]);
    }

    public function bulkAction(Request $request): RedirectResponse
    {
        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
            'action' => 'required|in:approve,reject',
        ]);

        $users = User::whereIn('id', $request->user_ids)->get();

        if ($request->action === 'approve') {
            foreach ($users as $user) {
                $user->update(['is_approved' => true]);
            }
            $message = count($users).' users approved successfully.';
        } elseif ($request->action === 'reject') {
            foreach ($users as $user) {
                $user->delete();
            }
            $message = count($users).' users rejected and removed.';
        }

        return redirect()->back()->with('status', $message);
    }

    public function approve(User $user): RedirectResponse
    {
        $user->update(['is_approved' => true]);

        return redirect()->back()->with('status', 'User approved successfully.');
    }

    public function reject(User $user): RedirectResponse
    {
        // Option 1: Delete the user
        $user->delete();

        // Option 2: Keep them but mark rejected (if you have a status column)
        // For now, deletion seems appropriate for a rejection of a registration request.

        return redirect()->back()->with('status', 'User rejected and removed.');
    }

    public function resetPassword(Request $request, User $user): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user->update([
            'password' => Hash::make($request->password),
            'encrypted_password' => Crypt::encryptString($request->password), // Store encrypted
        ]);

        return redirect()->back()->with('status', 'Password reset successfully.');
    }

    // Export a CSV list of student login usernames (emails) and status
    public function exportStudents(Request $request): StreamedResponse
    {
        $students = User::where('role', User::ROLE_STUDENT)->orderBy('name')->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="student_logins.csv"',
        ];

        return response()->stream(function () use ($students) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Name', 'Email (Username)', 'Status', 'Default Password Hint']);
            foreach ($students as $s) {
                $status = $s->is_approved ? 'Active' : ($s->has_requested_account ? 'Pending' : 'Imported');
                $hint = (! $s->has_requested_account && $s->role === User::ROLE_STUDENT)
                    ? strtolower(last(explode(' ', $s->name))).'123'
                    : '';
                fputcsv($out, [$s->name, $s->email, $status, $hint]);
            }
            fclose($out);
        }, 200, $headers);
    }
}
