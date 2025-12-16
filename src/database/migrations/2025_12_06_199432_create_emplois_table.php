<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('emplois', function (Blueprint $table) {
            $table->id();

            // Relation to groups table
            $table->unsignedBigInteger('group_id')->nullable();
            $table->foreign('group_id')->references('id')->on('groups')->onDelete('cascade');

            // Snapshot of group name (intitule)
            $table->string('group')->nullable();

            // Teacher name (no relation)
            $table->string('teacher_name')->nullable();

            // Academic info
            $table->string('level')->nullable();
            $table->string('subject')->nullable();

            // Salle
            $table->string('salle')->nullable();

            // Timing JSON - same structure as old groups.timing
            $table->json('timing')->nullable();

            // Type of timetable: default or period
            $table->enum('type', ['default', 'period'])->default('default');

            // Period date range (nullable except when type = period)
            $table->date('from')->nullable();
            $table->date('to')->nullable();

            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('emplois');
    }
};
