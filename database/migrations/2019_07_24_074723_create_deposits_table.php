<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDepositsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('deposits', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('tenant_contract_id')->comment('租客合約 ID');
            $table->date('deposit_collection_date')->comment('收訂日期');
            $table->string('deposit_collection_serial_number')->comment('收訂單號');
            $table->integer('deposit_confiscated_amount')->comment('沒定金額');
            $table->integer('deposit_returned_amount')->comment('退訂金額');
            $table->date('confiscated_or_returned_date')->comment('沒/退訂日期');
            $table->integer('invoicing_amount')->comment('應開立金額');
            $table->date('invoice_date')->comment('發票日期');
            $table->string('invoice_serial_number')->comment('發票號碼');
            $table->boolean('is_deposit_collected')->comment('已收訂');
            $table->text('comment')->comment('備註');

            $table->timestamps();
            $table->softDeletes();


            $table->foreign('tenant_contract_id')
                ->references('id')->on('tenant_contract');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('deposits');
    }
}
