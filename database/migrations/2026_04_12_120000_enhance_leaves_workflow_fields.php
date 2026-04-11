<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leaves', function (Blueprint $table) {
            $table->unsignedInteger('number_of_days')->nullable()->after('end_date');
            $table->string('attachment_path')->nullable()->after('reason');
            $table->timestamp('submitted_at')->nullable()->after('status');
            $table->timestamp('cancelled_at')->nullable()->after('submitted_at');
            $table->text('cancellation_reason')->nullable()->after('cancelled_at');
            $table->text('reviewer_remarks')->nullable()->after('cancellation_reason');
        });
    }

    public function down(): void
    {
        Schema::table('leaves', function (Blueprint $table) {
            $table->dropColumn([
                'number_of_days',
                'attachment_path',
                'submitted_at',
                'cancelled_at',
                'cancellation_reason',
                'reviewer_remarks',
            ]);
        });
    }
};
