<?php

use App\Http\Middleware\RoleMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => RoleMiddleware::class,
            'ojt_adviser' => \App\Http\Middleware\OjtAdviserMiddleware::class,
        ]);
        $middleware->prependToGroup('web', \App\Http\Middleware\EnsureSessionDriver::class);
        $middleware->appendToGroup('web', \App\Http\Middleware\NoCache::class);
        $middleware->appendToGroup('web', \App\Http\Middleware\SetLocale::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (TokenMismatchException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Page expired. Please refresh and try again.'], 419);
            }

            $request->session()->regenerateToken();

            return back()
                ->withInput($request->except('password'))
                ->withErrors(['email' => 'Page expired. Please refresh and try again.']);
        });
    })->create();
