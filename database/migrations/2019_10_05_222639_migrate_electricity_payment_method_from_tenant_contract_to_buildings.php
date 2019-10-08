<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MigrateElectricityPaymentMethodFromTenantContractToBuildings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \App\TenantContract::chunk(100, function($tenantContracts)
        {
            foreach($tenantContracts as $tenantContract)
            {
                $electricity_payment_method = $tenantContract->electricity_payment_method;
                $tenantContract->room->building->update(['electricity_payment_method' => $electricity_payment_method]);
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \App\TenantContract::chunk(100, function($tenantContracts)
        {
            foreach($tenantContracts as $tenantContract)
            {
                $electricity_payment_method = $tenantContract->electricity_payment_method;
                $tenantContract->room->building->update(['electricity_payment_method' => null]);
            }
        });
    }
}
