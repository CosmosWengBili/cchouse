<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MovePaymentColumnFromLandlordContractToLandlord extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('landlords', function (Blueprint $table) {
            $table->integer('bank_code')->comment('匯款銀行');
            $table->integer('branch_code')->comment('匯款分行');
            $table->string('account_name')->comment('戶名');
            $table->string('account_number')->comment('帳號');
            $table->string('invoice_collection_method')->comment('發票領取方式');
            $table->string('invoice_collection_number')->comment('發票領取號碼');
            $table->string('invoice_mailing_address')->comment('發票寄送地址');
        });
        Schema::table('landlord_contract', function (Blueprint $table) {
            $table->dropColumn('bank_code');
            $table->dropColumn('branch_code');
            $table->dropColumn('account_name');
            $table->dropColumn('account_number');
            $table->dropColumn('invoice_collection_method');
            $table->dropColumn('invoice_collection_number');
            $table->dropColumn('invoice_mailing_address');

            $table->dropForeign(['landlord_id']);
            $table->dropColumn('landlord_id');
            
        });
        Schema::table('tenants', function (Blueprint $table) {
            $table->date('birth')->comment('出生年月日');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('landlords', function (Blueprint $table) {
            $table->dropColumn('bank_code');
            $table->dropColumn('branch_code');
            $table->dropColumn('account_name');
            $table->dropColumn('account_number');
            $table->dropColumn('invoice_collection_method');
            $table->dropColumn('invoice_collection_number');
            $table->dropColumn('invoice_mailing_address');
        });
        Schema::table('landlord_contract', function (Blueprint $table) {
            $table->integer('bank_code')->comment('匯款銀行');
            $table->integer('branch_code')->comment('匯款分行');
            $table->string('account_name')->comment('戶名');
            $table->string('account_number')->comment('帳號');
            $table->string('invoice_collection_method')->comment('發票領取方式');
            $table->string('invoice_collection_number')->comment('發票領取號碼');
            $table->string('invoice_mailing_address')->comment('發票寄送地址');

            $table->foreign('landlord_id') 
            ->references('id')
            ->on('landlords');
        });
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn('birth');
        });
    }
}
