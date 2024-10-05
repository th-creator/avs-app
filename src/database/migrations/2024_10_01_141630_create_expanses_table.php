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
        Schema::create('expanses', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->decimal('amount', total: 8, places: 2)->nullable();
            $table->date('date')->nullable();
            $table->string('type')->nullable();
            $table->string('bank')->nullable();
            $table->string('bank_receipt')->nullable();
            $table->string('file')->nullable();
            $table->string('paid_by')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
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
