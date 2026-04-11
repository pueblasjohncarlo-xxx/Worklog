<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add leave balance tracking if not exists
        if (!Schema::hasColumn('assignments', 'leave_balance')) {
            Schema::table('assignments', function (Blueprint $table) {
                $table->unsignedInteger('leave_balance')->default(0)->after('required_hours');
                $table->unsignedInteger('annual_leave_limit')->default(15)->after('leave_balance');
                $table->unsignedInteger('sick_leave_limit')->default(10)->after('annual_leave_limit');
                $table->timestamp('leave_balance_reset_at')->nullable()->after('sick_leave_limit');
            });
        }

        // Add details for better tracking
        if (!Schema::hasColumn('leaves', 'days_remaining')) {
            Schema::table('leaves', function (Blueprint $table) {
                $table->integer('days_remaining')->nullable()->after('number_of_days');
                $table->text('approval_timeline')->nullable()->after('reviewer_remarks');
            });
        }
    }

    public function down(): void
    {
        Schema::table('assignments', function (Blueprint $table) {
            $table->dropColumn(['leave_balance', 'annual_leave_limit', 'sick_leave_limit', 'leave_balance_reset_at']);
        });

        Schema::table('leaves', function (Blueprint $table) {
            $table->dropColumn(['days_remaining', 'approval_timeline']);
        });
    }
};
