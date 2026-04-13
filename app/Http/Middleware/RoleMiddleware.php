<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user() ?? Auth::user();

        if (! $user) {
            abort(403);
        }

        if ($roles === []) {
            return $next($request);
        }

        $allowedRoles = collect($roles)
            ->flatMap(fn (string $role) => preg_split('/[|,]/', $role))
            ->filter()
            ->map(fn (string $role) => trim($role))
            ->values()
            ->all();

        if (! in_array($user->role, $allowedRoles, true)) {
            abort(403);
        }

        return $next($request);
    }
}
