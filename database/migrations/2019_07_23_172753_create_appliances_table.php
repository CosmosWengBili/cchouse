<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAppliancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('appliances', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('room_id')->comment('室ID');
            $table->string('subject')->comment('項目');
            $table->string('spec_code')->comment('型號');
            $table->string('vendor')->comment('廠商');
            $table->integer('count')->comment('個數');
            $table->string('maintenance_phone')->comment('維護電話');
            $table->text('comment')->comment('備註');
            
            $table->timestamps();
            $table->softDeletes();


            $table->foreign('room_id')
                ->references('id')->on('rooms');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('appliances');
    }
}
