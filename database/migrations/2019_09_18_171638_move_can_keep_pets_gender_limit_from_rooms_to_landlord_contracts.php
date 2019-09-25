<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MoveCanKeepPetsGenderLimitFromRoomsToLandlordContracts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->dropColumn('can_keep_pets');
            $table->dropColumn('gender_limit');
        });
        Schema::table('landlord_contracts', function (Blueprint $table) {
            $table->boolean('can_keep_pets')->comment('養寵物');
            $table->string('gender_limit')->comment('性別限制');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->boolean('can_keep_pets')->comment('養寵物');
            $table->string('gender_limit')->comment('性別限制');
        });
        Schema::table('landlord_contracts', function (Blueprint $table) {
            $table->dropColumn('can_keep_pets');
            $table->dropColumn('gender_limit');
        });
    }
}
