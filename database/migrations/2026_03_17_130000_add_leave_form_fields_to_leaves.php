<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leaves', function (Blueprint $table) {
            $table->string('student_name')->nullable()->after('signature_path');
            $table->string('course_major')->nullable()->after('student_name');
            $table->string('year_section')->nullable()->after('course_major');
            $table->string('cellphone_no')->nullable()->after('year_section');
            $table->string('company_name')->nullable()->after('cellphone_no');
            $table->date('date_filed')->nullable()->after('company_name');
            $table->string('job_designation')->nullable()->after('date_filed');
            $table->string('prepared_by')->nullable()->after('job_designation');
        });
    }

    public function down(): void
    {
        Schema::table('leaves', function (Blueprint $table) {
            $table->dropColumn([
                'student_name',
                'course_major',
                'year_section',
                'cellphone_no',
                'company_name',
                'date_filed',
                'job_designation',
                'prepared_by',
            ]);
        });
    }
};
