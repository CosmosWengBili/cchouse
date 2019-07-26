<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateKeyRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('key_requests', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('request_user_id')->comment('借用人');
            $table->unsignedBigInteger('key_id')->comment('鑰匙 ID');
            $table->string('status')->comment('狀態');
            $table->date('request_date')->nullable()->comment('出借日');
            $table->boolean('request_approved')->comment('出借允許');

            $table->timestamps();
            $table->softDeletes();


            $table->foreign('request_user_id')
                ->references('id')->on('users');
            $table->foreign('key_id')
                ->references('id')->on('keys');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('key_requests');
    }
}
