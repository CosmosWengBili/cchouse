<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePayLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pay_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('loggable_type')->comment('tenant_payment, tenant_electricity_payment');
            $table->unsignedBigInteger('loggable_id')->comment('費用 ID (PK)');
            $table->string('subject')->comment('科目');
            $table->string('payment_type')->comment('繳費類別');
            $table->integer('amount')->comment('費用');
            $table->string('virtual_account')->comment('虛擬帳號');
            $table->timestamp('paid_at')->nullable()->comment('匯款時間');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pay_logs');
    }
}
