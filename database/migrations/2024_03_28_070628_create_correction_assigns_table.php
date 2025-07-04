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
        Schema::create('correction_assigns', function (Blueprint $table) {
            $table->id();
            $table->string('correction_id');
            $table->string('fwd_by');
            $table->string('fwd_by_reason');
            $table->string('fwd_to');
            $table->string('fwd_to_reason');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('correction_assigns');
    }
};
