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
        Schema::create('suwasahana_failures', function (Blueprint $table) {
            $table->id();
            $table->string('regimental_number');
            $table->string('rank')->nullable();
            $table->string('name')->nullable();
            $table->double('capital')->nullable();
            $table->double('interest')->nullable();
            $table->string('error')->nullable();
            $table->integer('year')->nullable();
            $table->integer('month')->nullable();
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suwasahana_failures');
    }
};
