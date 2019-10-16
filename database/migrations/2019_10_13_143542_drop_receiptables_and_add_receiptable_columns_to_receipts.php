<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropReceiptablesAndAddReceiptableColumnsToReceipts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('receiptables');
        Schema::table('receipts', function (Blueprint $table) {
            $table->unsignedBigInteger('receiptable_id')->after('id');
            $table->string('receiptable_type')->after('receiptable_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('receipts', function (Blueprint $table) {
            $table->dropColumn('receiptable_id');
            $table->dropColumn('receiptable_type');
        });
        Schema::create('receiptables', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer("receipt_id");
            $table->integer("receiptable_id");
            $table->string("receiptable_type");
        
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
