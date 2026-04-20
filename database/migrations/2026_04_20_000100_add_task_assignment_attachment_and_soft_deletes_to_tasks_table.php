<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            if (! Schema::hasColumn('tasks', 'task_attachment_path')) {
                $table->string('task_attachment_path')->nullable()->after('original_filename');
            }
            if (! Schema::hasColumn('tasks', 'task_original_filename')) {
                $table->string('task_original_filename')->nullable()->after('task_attachment_path');
            }
            if (! Schema::hasColumn('tasks', 'deleted_at')) {
                $table->softDeletes();
            }
        });
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            if (Schema::hasColumn('tasks', 'deleted_at')) {
                $table->dropSoftDeletes();
            }
            if (Schema::hasColumn('tasks', 'task_original_filename')) {
                $table->dropColumn('task_original_filename');
            }
            if (Schema::hasColumn('tasks', 'task_attachment_path')) {
                $table->dropColumn('task_attachment_path');
            }
        });
    }
};
