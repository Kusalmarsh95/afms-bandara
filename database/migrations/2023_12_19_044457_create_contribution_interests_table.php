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
        Schema::create('contribution_interests', function (Blueprint $table) {
            $table->id();
            $table->integer('version');
            $table->integer('year');
            $table->integer('icp_id');
            $table->double('interest_rate');
            $table->string('status');
            $table->string('created_by');
            $table->string('created_system');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contribution_interests');
    }
};
