<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
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
