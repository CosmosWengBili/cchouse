<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDueTimeToTenantElectricityPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tenant_electricity_payments', function (Blueprint $table) {
            $table->date('due_time')->comment('應繳日期');
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
            $table->dropColumn('due_time');
        });
    }
}
