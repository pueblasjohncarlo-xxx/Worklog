<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('has_requested_account')->default(false)->after('is_approved');
        });

        // Optional: Attempt to preserve the specific user 'ponteras.beejay@llcc.edu.ph' if they exist
        DB::table('users')->where('email', 'ponteras.beejay@llcc.edu.ph')->update(['has_requested_account' => true]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('has_requested_account');
        });
    }
};
