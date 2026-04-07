<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Ensure any existing users without a password can log in (set a temporary password).
        \App\Models\User::where(function ($q) {
            $q->whereNull('password')->orWhere('password', '');
        })->chunkById(100, function ($users) {
            foreach ($users as $user) {
                $user->password = 'password123';
                $user->save();
            }
        });

        $admin = User::updateOrCreate(
            ['email' => 'admin@worklog.local'],
            [
                'name' => 'System Admin',
                'password' => 'password123',
                'role' => User::ROLE_ADMIN,
                'is_approved' => true,
                'has_requested_account' => true,
            ],
        );
        $admin->forceFill(['email_verified_at' => now()])->save();

        $coordinator = User::updateOrCreate(
            ['email' => 'mark@gmail.com'],
            [
                'name' => 'Mark Roble',
                'password' => 'marko123',
                'role' => User::ROLE_COORDINATOR,
                'is_approved' => true,
                'has_requested_account' => true,
            ],
        );
        $coordinator->forceFill(['email_verified_at' => now()])->save();
    }
}
