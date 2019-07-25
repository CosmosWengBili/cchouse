<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShareholdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shareholders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->comment('姓名');
            $table->string('email')->comment('Email');
            $table->string('bank_name')->comment('銀行名稱');
            $table->string('bank_code')->comment('銀行代號');
            $table->string('account_number')->comment('銀行號碼');
            $table->string('account_name')->comment('銀行戶名');
            $table->boolean('is_remittance_fee_collected')->comment('匯費收取');
            $table->string('transfer_from')->comment('匯出銀行');
            $table->string('bill_delivery')->comment('帳單郵寄或傳真');
            $table->string('distribution_method')->comment('分配方式');
            $table->date('distribution_start_date')->comment('分配起');
            $table->date('distribution_end_date')->comment('分配迄');
            $table->integer('distribution_rate')->comment('分配費率');
            $table->integer('investment_amount')->comment('投資額');    

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
        Schema::dropIfExists('shareholders');
    }
}
