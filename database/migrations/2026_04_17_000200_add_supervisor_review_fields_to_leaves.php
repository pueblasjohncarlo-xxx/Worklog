<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leaves', function (Blueprint $table) {
            if (! Schema::hasColumn('leaves', 'supervisor_decision')) {
                $table->string('supervisor_decision', 20)->nullable()->after('status');
            }

            if (! Schema::hasColumn('leaves', 'supervisor_reviewer_id')) {
                $table->foreignId('supervisor_reviewer_id')->nullable()->after('supervisor_decision')
                    ->constrained('users')->nullOnDelete();
            }

            if (! Schema::hasColumn('leaves', 'supervisor_reviewed_at')) {
                $table->timestamp('supervisor_reviewed_at')->nullable()->after('supervisor_reviewer_id');
            }

            if (! Schema::hasColumn('leaves', 'supervisor_reviewer_remarks')) {
                $table->text('supervisor_reviewer_remarks')->nullable()->after('supervisor_reviewed_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('leaves', function (Blueprint $table) {
            $columns = [
                'supervisor_reviewer_remarks',
                'supervisor_reviewed_at',
                'supervisor_reviewer_id',
                'supervisor_decision',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('leaves', $column)) {
                    if ($column === 'supervisor_reviewer_id') {
                        $table->dropConstrainedForeignId($column);
                    } else {
                        $table->dropColumn($column);
                    }
                }
            }
        });
    }
};
