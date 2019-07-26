<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateKeysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('keys', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('key_name')->comment('鑰匙代號');
            $table->unsignedBigInteger('keeper_id')->comment('保管人');
            $table->unsignedBigInteger('room_id')->comment('室ID');

            $table->timestamps();
            $table->softDeletes();
            

            $table->foreign('keeper_id')
                ->references('id')->on('users');
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
        Schema::dropIfExists('keys');
    }
}
