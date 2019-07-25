<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLandlordPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('landlord_payments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('building_id')->comment('物件ID');
            $table->string('subject')->comment('項目');
            $table->string('bill_serial_number')->comment('帳單號');
            $table->date('collection_date')->comment('收帳日');
            $table->string('billing_vendor')->comment('帳單廠商');
            $table->date('bill_start_date')->comment('帳單期初');
            $table->date('bill_end_date')->comment('帳單期末');
            $table->integer('amount')->comment('費用');
            $table->text('comment')->comment('備註');

            $table->timestamps();
            $table->softDeletes();


            $table->foreign('building_id')
                ->references('id')->on('buildings');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('landlord_payments');
    }
}
