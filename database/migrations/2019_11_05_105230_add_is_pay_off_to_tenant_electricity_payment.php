<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIsPayOffToTenantElectricityPayment extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tenant_electricity_payments', function (Blueprint $table) {
            $table->boolean('is_pay_off')->default(0)->after('is_charge_off_done')->comment('是否為點交');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tenant_electricity_payments', function (Blueprint $table) {
            $table->dropColumn('is_pay_off');
        });
    }
}
