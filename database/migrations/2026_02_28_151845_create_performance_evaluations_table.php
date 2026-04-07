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
        Schema::create('performance_evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('supervisor_id')->constrained('users')->onDelete('cascade');
            $table->date('evaluation_date');

            // Ratings (1-5)
            $table->integer('attendance_punctuality'); // Punctuality and regular attendance
            $table->integer('quality_of_work'); // Accuracy, neatness, and thoroughness
            $table->integer('initiative'); // Self-starter, resourceful
            $table->integer('cooperation'); // Ability to work with others
            $table->integer('dependability'); // Reliability in completing tasks
            $table->integer('communication_skills'); // Written and verbal

            $table->text('remarks')->nullable();
            $table->decimal('final_rating', 3, 2); // Average of scores
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('performance_evaluations');
    }
};
