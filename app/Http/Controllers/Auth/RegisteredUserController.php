<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\RegistrationInvitation;
use App\Models\SupervisorProfile;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(Request $request): View
    {
        $inviteToken = $request->query('invite');
        $invitation = $this->resolveValidInvitation(is_string($inviteToken) ? $inviteToken : null);

        return view('auth.register', [
            'companies' => Company::orderBy('name')->get(),
            'invitation' => $invitation,
            'inviteToken' => $invitation ? $inviteToken : null,
            'inviteError' => $inviteToken && ! $invitation
                ? 'This invitation link is invalid, expired, or already used.'
                : null,
        ]);
    }

    /**
     * Handle an incoming registration request.
     */
    public function store(Request $request): RedirectResponse
    {
        $inviteToken = $request->input('invite_token');
        $invitation = $this->resolveValidInvitation(is_string($inviteToken) ? $inviteToken : null);

        if (! empty($inviteToken) && ! $invitation) {
            return back()->withInput()->withErrors([
                'email' => 'Your invitation link is invalid, expired, or already used.',
            ]);
        }

        if ($invitation) {
            $request->merge([
                'email' => $invitation->email,
                'role' => $invitation->role,
                'company_id' => $invitation->company_id ?? $request->input('company_id'),
            ]);
        }

        $request->merge([
            'role' => $request->input('role', User::ROLE_STUDENT),
        ]);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'string', Rule::in([User::ROLE_STUDENT, User::ROLE_SUPERVISOR, User::ROLE_OJT_ADVISER])],
            'company_id' => ['required_if:role,'.User::ROLE_SUPERVISOR, 'nullable', 'integer', 'exists:companies,id'],
            'section' => ['required_if:role,'.User::ROLE_STUDENT, 'nullable', 'string', Rule::in(User::STUDENT_SECTIONS)],
            'department' => ['required_if:role,'.User::ROLE_STUDENT, 'nullable', 'string', Rule::in(User::STUDENT_MAJORS)],
        ], [
            'company_id.required_if' => 'Please select a company for supervisor registration.',
            'company_id.exists' => 'The selected company is invalid.',
        ]);

        $role = $validated['role'] ?? User::ROLE_STUDENT;
        $normalizedDepartment = $role === User::ROLE_STUDENT
            ? User::normalizeStudentDepartment($validated['department'] ?? null)
            : null;
        $normalizedSection = $role === User::ROLE_STUDENT
            ? User::normalizeStudentSection($validated['section'] ?? null, $normalizedDepartment)
            : null;
        $email = $request->email;
        $existingUser = User::where('email', $email)->first();

        if ($existingUser) {
            return back()->withErrors(['email' => 'This email is already registered.']);
        }

        // Build user data with all required fields
        $userData = [
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'encrypted_password' => Crypt::encryptString($request->password),
            'role' => $role,
            'section' => $normalizedSection,
            'department' => $normalizedDepartment,
            'has_requested_account' => true,
        ];

        // Publicly registered accounts require coordinator approval.
        if (Schema::hasColumn('users', 'is_approved')) {
            $userData['is_approved'] = false;
        }

        if (Schema::hasColumn('users', 'status')) {
            $userData['status'] = 'pending';
        }

        if (Schema::hasColumn('users', 'approved_at')) {
            $userData['approved_at'] = null;
        }

        if (Schema::hasColumn('users', 'rejected_at')) {
            $userData['rejected_at'] = null;
        }

        if (Schema::hasColumn('users', 'rejection_reason')) {
            $userData['rejection_reason'] = null;
        }

        $user = DB::transaction(function () use ($userData, $role, $validated, $invitation) {
            $lockedInvitation = null;
            if ($invitation) {
                $lockedInvitation = RegistrationInvitation::query()
                    ->whereKey($invitation->id)
                    ->whereNull('accepted_at')
                    ->whereNull('revoked_at')
                    ->where('expires_at', '>', now())
                    ->lockForUpdate()
                    ->first();

                if (! $lockedInvitation) {
                    throw ValidationException::withMessages([
                        'email' => 'This invitation link is no longer valid. Please request a new invitation.',
                    ]);
                }
            }

            $user = User::create($userData);

            if ($role === User::ROLE_SUPERVISOR) {
                SupervisorProfile::create([
                    'user_id' => $user->id,
                    'company_id' => (int) $validated['company_id'],
                    'position_title' => null,
                    'department' => null,
                    'phone' => null,
                ]);
            }

            if ($lockedInvitation) {
                $lockedInvitation->update([
                    'accepted_at' => now(),
                ]);
            }

            return $user;
        });

        return redirect()->route('login')->with('status', 'Your account has been created and is pending coordinator approval.');
    }

    private function resolveValidInvitation(?string $token): ?RegistrationInvitation
    {
        if (is_null($token) || trim($token) === '') {
            return null;
        }

        $tokenHash = hash('sha256', trim($token));

        return RegistrationInvitation::query()
            ->where('token_hash', $tokenHash)
            ->whereNull('accepted_at')
            ->whereNull('revoked_at')
            ->where('expires_at', '>', now())
            ->first();
    }
}
