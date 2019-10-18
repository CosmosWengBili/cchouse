<?php

use Illuminate\Database\Seeder;

/**
 * 針對 Landlord, Landlord Contract, Building,
 * Room, Appliance, Tenant, Tenant Contract,
 * Tenant Payment, Tenant Electricity Payment,
 * Shareholder 設計預設資料
 * Class FakeDataSeeder
 */
class FakeDataSeeder extends Seeder
{
    const SEEDER_TABLES = [
        'landlords', 'landlord_contracts', 'landlord_landlord_contract', 'buildings', 'rooms',
        'appliances', 'tenants', 'tenant_contract', 'tenant_payments', 'tenant_electricity_payments',
        'shareholders', 'building_shareholder', 'pay_logs', 'pay_offs'
    ];


    /**
     * php artisan db:seed --class=FakeDataSeeder --env=testing
     *
     * @return void
     */
    public function run()
    {
//        $this->truncate();

        factory(\App\LandlordContract::class, 3)->create();

        \App\Building::all()->each(function (\App\Building $building) {
            $building->rooms()->create();
            $building->shareholders()->create();
        });

        \App\LandlordContract::all()->each(function (\App\LandlordContract $landlord_contract) {
            $landlord_contract->landlords()->create();
        });

        \App\Room::all()->each(function (\App\Room $room) {
            $room->appliances()->create();
            $room->tenantContracts()->create([
                'tenant_id' => factory(\App\Tenant::class)->create()->id,
            ]);
        });

        \App\TenantContract::all()->each(function (\App\TenantContract $tenant_contract) {
            $tenant_contract->tenantPayments()->create();
            $tenant_contract->tenantElectricityPayments()->create();

            $tenant_contract->companyIncomes()->create();
            $tenant_contract->payOff()->create([
                'pay_off_type' => '協調退租'
            ]);
            $tenant_contract->payOff()->create([
                'pay_off_type' => '中途退租'
            ]);
            $tenant_contract->maintenances()->create();

        });

        \App\TenantPayment::all()->each(function (\App\TenantPayment $tenant_payment) {
            $tenant_payment->payLogs()->create([
                'loggable_type' => 'tenant_payment',
                'payment_type' => \App\TenantPayment::class,
                'receipt_type' => '發票',
            ]);
        });

//        dd(
//            'Landlord',
//            \App\Landlord::all()->count(),
//            'LandlordContract',
//            \App\LandlordContract::all()->count(),
//            'Building',
//            \App\Building::all()->count(),
//            'Room',
//            \App\Room::all()->count(),
//            'Appliance',
//            \App\Appliance::all()->count(),
//            'Tenant',
//            \App\Tenant::all()->count(),
//            'TenantContract',
//            \App\TenantContract::all()->count(),
//            'TenantPayment',
//            \App\TenantPayment::all()->count(),
//            'TenantElectricityPayment',
//            \App\TenantElectricityPayment::all()->count(),
//            'Shareholder',
//            \App\Shareholder::all()->count()
//        );
    }

    private function truncate()
    {
        \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();

        foreach (self::SEEDER_TABLES as $table) {
            \Illuminate\Support\Facades\DB::table($table)->truncate();
        }

        \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();
    }
}
