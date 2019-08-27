<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddReceiptTableAndRemoveOtherTableInvoiceSerialNumber extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('receipts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamp('date')->comment('開立日期');
            $table->string('invoice_serial_number')->comment('發票號碼');
            $table->string('invoice_price')->comment('發票金額');
            

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('receiptables', function (Blueprint $table) {
            $table->integer("receipt_id");
            $table->integer("receiptable_id");
            $table->string("receiptable_type");
        
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::table('deposits', function (Blueprint $table) {
            $table->dropColumn('invoice_serial_number');
        });
        Schema::table('tenant_electricity_payments', function (Blueprint $table) {
            $table->dropColumn('invoice_serial_number');
        });
        Schema::table('tenant_payments', function (Blueprint $table) {
            $table->dropColumn('invoice_serial_numner');
        });
        Schema::table('debt_collections', function (Blueprint $table) {
            $table->dropColumn('invoice_serail_number');
        });
        Schema::table('landlord_payments', function (Blueprint $table) {
            $table->dropColumn('invoice_serail_number');
        });


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('receipts');
        Schema::dropIfExists('receiptables');
        Schema::table('deposits', function (Blueprint $table) {
            $table->string('invoice_serial_number')->comment('發票號碼');
        });
        Schema::table('tenant_electricity_payments', function (Blueprint $table) {
            $table->string('invoice_serial_number')->comment('發票號碼');
        });
        Schema::table('tenant_payments', function (Blueprint $table) {
            $table->string('invoice_serial_numner')->comment('發票號碼');
        });
        Schema::table('debt_collections', function (Blueprint $table) {
            $table->string('invoice_serail_number')->comment('發票號碼');
        });
        Schema::table('landlord_payments', function (Blueprint $table) {
            $table->string('invoice_serail_number')->comment('發票號碼');
        });
    }
}
