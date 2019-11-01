<?php

use Illuminate\Database\Seeder;
use Faker\Generator as Faker;

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
        $this->truncate();

        factory(\App\LandlordContract::class, 2)->create();

        \App\LandlordContract::all()->each(function (\App\LandlordContract $landlord_contract) {
            $landlord_contract->landlords()->save(factory(\App\Landlord::class)->create());
        });

        \App\Building::all()->each(function (\App\Building $building) {
            $building->shareholders()->save(factory(\App\Shareholder::class)->create());
            $building->rooms()->saveMany(factory(\App\Room::class, 1)->make([
                'building_id' => $building->id,
                'room_layout' => '公區'
            ]));
        });

        \App\Room::all()->each(function (\App\Room $room) {
            $room->appliances()->save(factory(\App\Appliance::class)->create());
            $room->tenantContracts()->save(factory(\App\TenantContract::class)->make([
                'tenant_id' => factory(\App\Tenant::class)->create()->id,
                'room_id' => $room->id,
            ]));
            $room->keys()->save(factory(\App\Key::class)->make([
                'room_id' => $room->id,
            ]));
        });

        \App\Key::all()->each(function (\App\Key $key) {
            $key->keyRequests()->saveMany(factory(\App\KeyRequest::class, 3)->make([
                'key_id' => $key->id,
                'request_user_id' => $key->keeper_id
            ]));
        });

        \App\TenantContract::all()->each(function (\App\TenantContract $tenant_contract) {
            $tenant_contract->tenantPayments()->save(factory(\App\TenantPayment::class)->make([
                'tenant_contract_id' => $tenant_contract->id,
                'subject' => '履約保證金',
                'collected_by' => '房東',
                'amount'=> $tenant_contract->deposit,
                'period'       => '次',
                'comment' => '初次履約金',
            ]));

            $tenant_contract->tenantElectricityPayments()->save(factory(\App\TenantElectricityPayment::class)->make([
                'tenant_contract_id' => $tenant_contract->id
            ]));

            $tenant_contract->companyIncomes()->save(factory(\App\CompanyIncome::class)->make([
                'incomable_id' => $tenant_contract->id,
                'incomable_type' => \App\TenantContract::class
            ]));

            $tenant_contract->payOff()->create([
                'pay_off_type' => '協調退租'
            ]);
            $tenant_contract->payOff()->create([
                'pay_off_type' => '中途退租'
            ]);
            $tenant_contract->maintenances()->saveMany(factory(\App\Maintenance::class, 5)->make([
                'tenant_contract_id' => $tenant_contract->id,
                'room_id' => $tenant_contract->room_id,
            ]));
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
