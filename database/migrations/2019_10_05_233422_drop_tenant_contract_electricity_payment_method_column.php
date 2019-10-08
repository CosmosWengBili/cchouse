<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropTenantContractElectricityPaymentMethodColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tenant_contract', function (Blueprint $table) {
            $table->dropColumn('electricity_payment_method');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tenant_contract', function (Blueprint $table) {
            $table->string('electricity_payment_method')->comment('電費繳款方式');
        });
    }
}
