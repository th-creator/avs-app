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
        Schema::create('student_ay_numbers', function (Blueprint $table) {
            $table->id();
            $table->string('ay', 9); // e.g. "2025/2026"
            
            $table->unsignedBigInteger('student_id');
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            
            $table->unsignedInteger('num'); // sequential number in this AY
            
            $table->timestamps();

            // Constraints
            $table->unique(['ay', 'student_id']); // one number per student per AY
            $table->unique(['ay', 'num']);        // each number used once per AY
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_ay_numbers');
    }
};
