<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLandlordLandlordContract extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('landlord_landlord_contract', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('landlord_id')->unsigned();
            $table->bigInteger('landlord_contract_id')->unsigned();
            $table->foreign('landlord_id')->references('id')->on('landlords');
            $table->foreign('landlord_contract_id')->references('id')->on('landlord_contract');
            $table->timestamps();
        });

        Schema::rename('landlord_contract', 'landlord_contracts');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('landlord_landlord_contract');
        Schema::rename('landlord_contracts', 'landlord_contract');
    }
}
