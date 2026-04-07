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
        Schema::table('tasks', function (Blueprint $table) {
            $table->text('supervisor_note')->nullable()->after('grade');
            $table->string('supervisor_attachment_path')->nullable()->after('supervisor_note');
            $table->string('supervisor_original_filename')->nullable()->after('supervisor_attachment_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn(['supervisor_note', 'supervisor_attachment_path', 'supervisor_original_filename']);
        });
    }
};
