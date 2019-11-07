<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveTenantContractIdFromMaintenancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();
        Schema::table('maintenances', function (Blueprint $table) {
            $table->dropForeign('maintenance_tenant_contract_id_foreign');
            $table->dropColumn('tenant_contract_id');

            // update foreign
            $table->dropForeign('maintenance_commissioner_id_foreign');
            $table->dropForeign('maintenance_maintenance_staff_id_foreign');

            $table->foreign('commissioner_id')
                ->references('id')->on('users');

            $table->foreign('maintenance_staff_id')
            ->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::disableForeignKeyConstraints();
        Schema::table('maintenances', function (Blueprint $table) {
            $table->unsignedBigInteger('tenant_contract_id')->after('id')->comment('租客合約 ID');

            $table->foreign('tenant_contract_id')
            ->references('id')->on('tenant_contract');
        });
    }
}
