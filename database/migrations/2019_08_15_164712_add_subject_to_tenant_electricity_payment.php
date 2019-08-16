<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSubjectToTenantElectricityPayment extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tenant_electricity_payments', function (Blueprint $table) {
            $table->string('subject')->comment('項目')->default('電費')->after('tenant_contract_id');;
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
            $table->dropColumn('subject');
        });
    }
}
