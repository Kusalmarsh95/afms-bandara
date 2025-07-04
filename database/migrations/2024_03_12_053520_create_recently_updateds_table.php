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
        Schema::create('recently_updateds', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('regiment_id');
            $table->bigInteger('category_id');
            $table->bigInteger('year');
            $table->bigInteger('icp_id');
            $table->bigInteger('count');
            $table->date('date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recently_updateds');
    }
};
