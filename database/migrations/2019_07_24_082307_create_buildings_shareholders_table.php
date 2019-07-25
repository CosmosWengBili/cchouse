<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBuildingsShareholdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('buildings_shareholders', function (Blueprint $table) {
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
        Schema::dropIfExists('buildings_shareholders');
    }
}
