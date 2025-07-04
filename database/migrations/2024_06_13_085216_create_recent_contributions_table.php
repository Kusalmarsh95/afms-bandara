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
        Schema::create('recent_contributions', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('regiment_id');
            $table->bigInteger('category_id');
            $table->bigInteger('year');
            $table->bigInteger('month');
            $table->bigInteger('pnr_count');
            $table->bigInteger('abf_count');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recent_contributions');
    }
};
