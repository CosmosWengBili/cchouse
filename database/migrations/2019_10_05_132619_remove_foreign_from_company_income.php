<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveForeignFromCompanyIncome extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('company_incomes', function (Blueprint $table) {
            $table->dropForeign('company_incomes_tenant_contract_id_foreign');
            $table->dropColumn('tenant_contract_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('company_incomes', function (Blueprint $table) {
            $table->unsignedBigInteger('tenant_contract_id');
            $table->foreign('tenant_contract_id') 
                ->references('id')
                ->on('tenant_contract');
        });
    }
}
