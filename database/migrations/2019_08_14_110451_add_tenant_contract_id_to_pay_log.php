<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTenantContractIdToPayLog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pay_logs', function (Blueprint $table) {
            $table->unsignedBigInteger('tenant_contract_id');
            $table->foreign('tenant_contract_id')
                ->references('id')->on('tenant_contract');
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
            $table->dropForeign(['tenant_contract_id']);
            $table->dropColumn('tenant_contract_id');
        });
    }
}
