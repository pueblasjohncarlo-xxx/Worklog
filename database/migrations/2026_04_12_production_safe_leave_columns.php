<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Production-safe migration that adds all missing leave and assignment columns
     * Checks if columns exist before creating them to avoid errors on re-run
     */
    public function up(): void
    {
        // Add missing numbered_of_days and related columns to leaves table
        Schema::table('leaves', function (Blueprint $table) {
            // Add number_of_days if it doesn't exist
            if (!Schema::hasColumn('leaves', 'number_of_days')) {
                $table->unsignedInteger('number_of_days')->nullable()->after('end_date');
            }

            // Add attachment_path if it doesn't exist
            if (!Schema::hasColumn('leaves', 'attachment_path')) {
                $table->string('attachment_path')->nullable()->after('reason');
            }

            // Add submitted_at if it doesn't exist
            if (!Schema::hasColumn('leaves', 'submitted_at')) {
                $table->timestamp('submitted_at')->nullable()->after('status');
            }

            // Add cancelled_at if it doesn't exist
            if (!Schema::hasColumn('leaves', 'cancelled_at')) {
                $table->timestamp('cancelled_at')->nullable()->after('submitted_at');
            }

            // Add cancellation_reason if it doesn't exist
            if (!Schema::hasColumn('leaves', 'cancellation_reason')) {
                $table->text('cancellation_reason')->nullable()->after('cancelled_at');
            }

            // Add reviewer_remarks if it doesn't exist
            if (!Schema::hasColumn('leaves', 'reviewer_remarks')) {
                $table->text('reviewer_remarks')->nullable()->after('cancellation_reason');
            }

            // Add signature_path if it doesn't exist
            if (!Schema::hasColumn('leaves', 'signature_path')) {
                $table->string('signature_path')->nullable()->after('reviewer_remarks');
            }

            // Add student info fields if they don't exist
            if (!Schema::hasColumn('leaves', 'student_name')) {
                $table->string('student_name')->nullable()->after('signature_path');
            }

            if (!Schema::hasColumn('leaves', 'course_major')) {
                $table->string('course_major')->nullable()->after('student_name');
            }

            if (!Schema::hasColumn('leaves', 'year_section')) {
                $table->string('year_section')->nullable()->after('course_major');
            }

            if (!Schema::hasColumn('leaves', 'cellphone_no')) {
                $table->string('cellphone_no')->nullable()->after('year_section');
            }

            if (!Schema::hasColumn('leaves', 'company_name')) {
                $table->string('company_name')->nullable()->after('cellphone_no');
            }

            if (!Schema::hasColumn('leaves', 'date_filed')) {
                $table->date('date_filed')->nullable()->after('company_name');
            }

            if (!Schema::hasColumn('leaves', 'job_designation')) {
                $table->string('job_designation')->nullable()->after('date_filed');
            }

            if (!Schema::hasColumn('leaves', 'prepared_by')) {
                $table->string('prepared_by')->nullable()->after('job_designation');
            }

            // Add enhancement feature columns if they don't exist
            if (!Schema::hasColumn('leaves', 'days_remaining')) {
                $table->integer('days_remaining')->nullable()->after('number_of_days');
            }

            if (!Schema::hasColumn('leaves', 'approval_timeline')) {
                $table->text('approval_timeline')->nullable()->after('reviewer_remarks');
            }
        });

        // Add leave balance tracking to assignments table if columns don't exist
        Schema::table('assignments', function (Blueprint $table) {
            if (!Schema::hasColumn('assignments', 'leave_balance')) {
                $table->unsignedInteger('leave_balance')->default(0)->after('required_hours');
            }

            if (!Schema::hasColumn('assignments', 'annual_leave_limit')) {
                $table->unsignedInteger('annual_leave_limit')->default(15)->after('leave_balance');
            }

            if (!Schema::hasColumn('assignments', 'sick_leave_limit')) {
                $table->unsignedInteger('sick_leave_limit')->default(10)->after('annual_leave_limit');
            }

            if (!Schema::hasColumn('assignments', 'leave_balance_reset_at')) {
                $table->timestamp('leave_balance_reset_at')->nullable()->after('sick_leave_limit');
            }
        });
    }

    /**
     * Reverse the migration.
     */
    public function down(): void
    {
        Schema::table('leaves', function (Blueprint $table) {
            $columns = [
                'number_of_days',
                'attachment_path',
                'submitted_at',
                'cancelled_at',
                'cancellation_reason',
                'reviewer_remarks',
                'signature_path',
                'student_name',
                'course_major',
                'year_section',
                'cellphone_no',
                'company_name',
                'date_filed',
                'job_designation',
                'prepared_by',
                'days_remaining',
                'approval_timeline',
            ];

            // Only drop columns that exist
            foreach ($columns as $column) {
                if (Schema::hasColumn('leaves', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        Schema::table('assignments', function (Blueprint $table) {
            $columns = [
                'leave_balance',
                'annual_leave_limit',
                'sick_leave_limit',
                'leave_balance_reset_at',
            ];

            // Only drop columns that exist
            foreach ($columns as $column) {
                if (Schema::hasColumn('assignments', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
