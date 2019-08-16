<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsForReceiptUsage extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('debt_collections', function (Blueprint $table) {
            $table->string('invoice_serail_number')->comment('發票號碼')->after('is_penalty_collected');
        });
        Schema::table('landlord_payment', function (Blueprint $table) {
            $table->string('invoice_serail_number')->comment('發票號碼')->after('is_penalty_collected');
        });
        Schema::table('maintenances', function (Blueprint $table) {
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
        Schema::table('debt_collections', function (Blueprint $table) {
            $table->dropColumn('invoice_serail_number');
        });
        Schema::table('landlord_payment', function (Blueprint $table) {
            $table->dropColumn('invoice_serail_number');
        });
        Schema::table('maintenances', function (Blueprint $table) {
            $table->string('invoice_serail_number')->comment('發票號碼');
        });
    }
}
