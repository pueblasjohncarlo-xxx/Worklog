<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\Response;

class EnsureApprovedAccountAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user() ?? Auth::user();

        if (!$user) {
            return $next($request);
        }

        $state = $this->resolveAccessState($user);

        if ($state === 'approved') {
            return $next($request);
        }

        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        $message = $state === 'rejected'
            ? 'Your account has been rejected. Please contact the coordinator.'
            : 'Your account is pending coordinator approval.';

        return redirect()->route('login')->withErrors([
            'email' => $message,
        ]);
    }

    private function resolveAccessState(User $user): string
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
}
