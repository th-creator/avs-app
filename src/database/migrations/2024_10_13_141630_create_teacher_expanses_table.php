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
        Schema::create('teacher_expanses', function (Blueprint $table) {
            $table->id();
            $table->string('teacher')->nullable();
            $table->date('date')->nullable();
            $table->string('group')->nullable();
            $table->integer('total')->nullable();
            $table->integer('percentage')->nullable();
            $table->integer('amount')->nullable();
            $table->integer('rest')->nullable();
            $table->string('month')->nullable();
            $table->string('year')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->unsignedBigInteger('teacher_id')->nullable();
            $table->foreign('teacher_id')->references('id')->on('teachers')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expanses');
    }
};
