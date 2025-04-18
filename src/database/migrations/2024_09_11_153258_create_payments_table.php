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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('fullName')->nullable();
            $table->string('group')->nullable();
            $table->decimal('amount', total: 8, places: 2);
            $table->integer('reduction')->nullable();
            $table->string('rest')->nullable();
            $table->decimal('total', total: 8, places: 2)->nullable();
            $table->decimal('amount_paid', total: 8, places: 2)->nullable();
            $table->string('type')->nullable();
            $table->string('bank')->nullable();
            $table->string('bank_receipt')->nullable();
            $table->string('receipt')->nullable();
            $table->string('month')->nullable();
            $table->string('year')->nullable();
            $table->integer('paid')->default(0);
            $table->date('date')->nullable();
            $table->unsignedBigInteger('student_id')->nullable();
            $table->foreign('student_id')->references('id')->on('students')->onDelete('set null');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->unsignedBigInteger('group_id')->nullable();
            $table->foreign('group_id')->references('id')->on('groups')->onDelete('set null');
            $table->unsignedBigInteger('registrant_id')->nullable();
            $table->foreign('registrant_id')->references('id')->on('registrants')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
