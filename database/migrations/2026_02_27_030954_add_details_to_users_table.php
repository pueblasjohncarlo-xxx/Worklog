<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'lastname')) {
                $table->string('lastname')->nullable()->after('name');
            }
            if (! Schema::hasColumn('users', 'firstname')) {
                $table->string('firstname')->nullable()->after('lastname');
            }
            if (! Schema::hasColumn('users', 'middlename')) {
                $table->string('middlename')->nullable()->after('firstname');
            }
            if (! Schema::hasColumn('users', 'age')) {
                $table->integer('age')->nullable()->after('middlename');
            }
            if (! Schema::hasColumn('users', 'gender')) {
                $table->string('gender')->nullable()->after('age');
            }
            if (! Schema::hasColumn('users', 'last_login_at')) {
                $table->timestamp('last_login_at')->nullable()->after('updated_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['lastname', 'firstname', 'middlename', 'age', 'gender', 'last_login_at']);
        });
    }
};
