<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\Response;

class EnsureSessionDriver
{
    protected static ?bool $databaseSessionsReady = null;

    public function handle(Request $request, Closure $next): Response
    {
        if (config('session.driver') === 'database') {
            if (static::$databaseSessionsReady === null) {
                static::$databaseSessionsReady = $this->databaseSessionsTableExists();
            }

            if (! static::$databaseSessionsReady) {
                config([
                    'session.driver' => 'file',
                    'session.connection' => null,
                ]);
            }
        }

        return $next($request);
    }

    protected function databaseSessionsTableExists(): bool
    {
        $table = (string) config('session.table', 'sessions');
        $connection = config('session.connection');

        try {
            if ($connection) {
                $db = DB::connection($connection);
                $db->getPdo();
                return Schema::connection($connection)->hasTable($table);
            }

            $db = DB::connection(config('database.default'));
            $db->getPdo();

            return Schema::hasTable($table);
        } catch (\Throwable) {
            return false;
        }
    }
}
