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
        Schema::create('member_edits', function (Blueprint $table) {
            $table->id();
            $table->integer('membership_id');
            $table->boolean('army_id_edited')->default(false);
            $table->boolean('comment_edited')->default(false);
            $table->boolean('contribution_amount_edited')->default(false);
            $table->boolean('dateabfenlisted_edited')->default(false);
            $table->boolean('date_army_enlisted_edited')->default(false);
            $table->boolean('decorations_edited')->default(false);
            $table->boolean('dob_edited')->default(false);
            $table->boolean('email_edited')->default(false);
            $table->boolean('member_status_id_edited')->default(false);
            $table->boolean('name_edited')->default(false);
            $table->boolean('nic_edited')->default(false);
            $table->boolean('rank_id_edited')->default(false);
            $table->boolean('regiment_id_edited')->default(false);
            $table->boolean('regimental_number_edited')->default(false);
            $table->boolean('retirement_date_edited')->default(false);
            $table->boolean('serial_number_edited')->default(false);
            $table->boolean('telephone_home_edited')->default(false);
            $table->boolean('telephone_mobile_edited')->default(false);
            $table->boolean('type_edited')->default(false);
            $table->boolean('unit_id_edited')->default(false);
            $table->boolean('address1_edited')->default(false);
            $table->boolean('address2_edited')->default(false);
            $table->boolean('address3_edited')->default(false);
            $table->boolean('district_id_edited')->default(false);
            $table->boolean('address_edited')->default(false);
            $table->boolean('loan10month_edited')->default(false);
            $table->boolean('suwasahana_edited')->default(false);
            $table->boolean('account_no_edited')->default(false);
            $table->boolean('enumber_edited')->default(false);
            $table->boolean('bank_code_edited')->default(false);
            $table->boolean('bank_name_edited')->default(false);
            $table->boolean('branch_code_edited')->default(false);
            $table->boolean('branch_name_edited')->default(false);
            $table->boolean('currentuser_edited')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('member_edits');
    }
};
