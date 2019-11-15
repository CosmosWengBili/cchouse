<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePayOffDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pay_off_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('pay_off_id')->comment('點交ID');
            $table->string('detail_type')->comment('來源類型');
            $table->unsignedBigInteger('detail_id')->comment('來源ID');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pay_off_details');
    }
}
