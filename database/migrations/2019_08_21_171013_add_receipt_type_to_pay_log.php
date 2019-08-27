<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddReceiptTypeToPayLog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pay_logs', function (Blueprint $table) {
            $table->string('receipt_type')->comment('收據類型')->default('發票')->after('virtual_account');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pay_logs', function (Blueprint $table) {
            $table->dropColumn('receipt_type');
        });
    }
}
