<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReversalErrorCasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
//        name:string, date:datetime, status:string,  comment:string, pay_log_id:integer
        Schema::create('reversal_error_cases', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->dateTime('date');
            $table->string('status')->default('未結案');
            $table->string('comment')->default('');
            $table->unsignedBigInteger('pay_log_id');
            $table->timestamps();

            $table->foreign('pay_log_id')->references('id')->on('pay_logs');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reversal_error_cases');
    }
}
