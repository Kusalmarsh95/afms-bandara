<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInterestToContributionCorrectionTable extends Migration
{
    public function up()
    {
        Schema::table('contribution_correction', function (Blueprint $table) {
            $table->double('interest')->nullable();
        });
    }

    public function down()
    {
        Schema::table('contribution_correction', function (Blueprint $table) {
            $table->dropColumn('interest');
        });
    }
}
}
