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
        Schema::table('companies', function (Blueprint $table) {
            $table->string('type')->nullable()->after('industry'); // e.g., Private, Government, NGO
            $table->json('work_opportunities')->nullable()->after('country'); // List of work types
            $table->foreignId('default_supervisor_id')->nullable()->after('work_opportunities')->constrained('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropForeign(['default_supervisor_id']);
            $table->dropColumn(['type', 'work_opportunities', 'default_supervisor_id']);
        });
    }
};
