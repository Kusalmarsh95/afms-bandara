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
        Schema::create('repayment_failures', function (Blueprint $table) {
            $table->id();
            $table->string('enumber')->nullable();
            $table->string('regimental_number')->nullable();
            $table->string('category')->nullable();
            $table->string('rank')->nullable();
            $table->string('unit')->nullable();
            $table->string('name')->nullable();
            $table->double('amount')->nullable();
            $table->string('reason')->nullable();
            $table->string('year')->nullable();
            $table->string('month')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('repayment_failures');
    }
};
