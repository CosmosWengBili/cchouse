<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBuildingShareholderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('building_shareholder', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('building_id')->comment('物件 ID');
            $table->unsignedBigInteger('shareholder_id')->comment('股東 ID');
            
            $table->timestamps();
            $table->softDeletes();


            $table->foreign('building_id')
                ->references('id')->on('buildings');

            $table->foreign('shareholder_id')
                ->references('id')->on('shareholders');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('building_shareholder');
    }
}
