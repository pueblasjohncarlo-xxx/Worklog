<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(Request $request): View
    {
        $request->session()->regenerateToken();

        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $user = $request->user();
        $accessState = $this->resolveAccessState($user);

        if ($accessState === 'pending') {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()->withErrors([
                'email' => 'Your account is pending coordinator approval.',
            ]);
        }

        if ($accessState === 'rejected') {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()->withErrors([
                'email' => 'Your account has been rejected. Please contact the coordinator.',
            ]);
        }

        // Keep internal roles sign-in ready even when legacy live data marks them as pending.
        $normalizedRole = strtolower(trim((string) ($user->role ?? '')));
        if (in_array($normalizedRole, [User::ROLE_COORDINATOR, User::ROLE_ADMIN, User::ROLE_STAFF], true)) {
            $updates = [];

            if (Schema::hasColumn('users', 'status') && strtolower(trim((string) ($user->status ?? ''))) !== 'approved') {
                $updates['status'] = 'approved';
            }

            if (Schema::hasColumn('users', 'is_approved') && (bool) ($user->is_approved ?? false) !== true) {
                $updates['is_approved'] = true;
            }

            if (Schema::hasColumn('users', 'approved_at') && empty($user->approved_at)) {
                $updates['approved_at'] = now();
            }

            if ($updates !== []) {
                $user->forceFill($updates)->save();
            }
        }

        $request->session()->regenerate();

        // Update last login time when the column exists.
        if (Schema::hasColumn('users', 'last_login_at')) {
            $user->update([
                'last_login_at' => now(),
            ]);
        }

        return redirect()->intended(route('dashboard', absolute: false));
    }

    private function resolveAccessState(object $user): string
    {
        $normalizedRole = strtolower(trim((string) ($user->role ?? '')));
        $approvalGatedRoles = [
            User::ROLE_STUDENT,
            User::ROLE_SUPERVISOR,
            User::ROLE_OJT_ADVISER,
        ];

        // Only these self-registration roles should be blocked by pending checks.
        if (! in_array($normalizedRole, $approvalGatedRoles, true)) {
            return 'approved';
        }

        $hasRequestedAccount = Schema::hasColumn('users', 'has_requested_account')
            ? (bool) ($user->has_requested_account ?? false)
            : true;

        // Legacy gated-role records not created through self-registration should remain usable.
        if (! $hasRequestedAccount) {
            return 'approved';
        }

        if (Schema::hasColumn('users', 'status')) {
            $status = strtolower(trim((string) ($user->status ?? '')));

            if ($status !== '') {
                if (in_array($status, ['approved', 'active'], true)) {
                    return 'approved';
                }

                if ($status === 'rejected') {
                    return 'rejected';
                }

                return 'pending';
            }

            // Request-based account with empty status should stay pending.
            return 'pending';
        }

        if (Schema::hasColumn('users', 'rejected_at') && !empty($user->rejected_at)) {
            return 'rejected';
        }

        if (Schema::hasColumn('users', 'rejection_reason') && !empty($user->rejection_reason)) {
            return 'rejected';
        }

        if (Schema::hasColumn('users', 'is_approved') && (bool) $user->is_approved === true) {
            return 'approved';
        }

        if (Schema::hasColumn('users', 'is_approved') && (bool) $user->is_approved === false) {
            return 'pending';
        }

        return 'pending';
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
