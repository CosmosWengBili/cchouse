<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MakeDepositsColumnsNullable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('deposits', function (Blueprint $table) {
            $table->integer('deposit_confiscated_amount')->nullable()->comment('沒定金額')->change();
            $table->integer('deposit_returned_amount')->nullable()->comment('退訂金額')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('deposits', function (Blueprint $table) {
            $table->integer('deposit_confiscated_amount')->comment('沒定金額')->change();
            $table->integer('deposit_returned_amount')->comment('退訂金額')->change();
        });
    }
}
