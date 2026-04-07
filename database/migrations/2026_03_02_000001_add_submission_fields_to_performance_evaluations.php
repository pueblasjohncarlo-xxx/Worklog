<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('performance_evaluations', function (Blueprint $table) {
            $table->timestamp('submitted_at')->nullable()->after('final_rating');
            $table->string('document_path')->nullable()->after('submitted_at');
            $table->string('document_type', 10)->nullable()->after('document_path'); // doc or pdf
        });
    }

    public function down(): void
    {
        Schema::table('performance_evaluations', function (Blueprint $table) {
            $table->dropColumn(['submitted_at', 'document_path', 'document_type']);
        });
    }
};
