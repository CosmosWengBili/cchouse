<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class testTenantElectricityPaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $selected_month = Carbon::now();
        $end_date = $selected_month->copy()->endOfMonth();
        $start_date = $selected_month->copy()->startOfMonth();

        $room = App\Room::find(43);
        $room->tenantContracts()->saveMany(factory(App\TenantContract::class, 3)->make([
            'contract_start' => $start_date,
            'contract_end' => $end_date,
        ]))->each(function ($tenant_contract) {
            // $contract_start = $tenant_contract->contract_start;
            $ammeter_read_date = Carbon::now()->startOfMonth();

            for ($i=1; $i <= 5; $i++) {
                $tenant_contract->tenantElectricityPayments()->save(factory(App\TenantElectricityPayment::class)->make([
                    'ammeter_read_date' => $ammeter_read_date->copy()->subMonth(3)->addDay($i+rand(1, $i))
                ]));
            }

            for ($i=1; $i <= 5; $i++) {
                $tenant_contract->tenantElectricityPayments()->save(factory(App\TenantElectricityPayment::class)->make([
                    'ammeter_read_date' => $ammeter_read_date->copy()->subMonth(2)->addDay($i+rand(1, $i))
                ]));
            }

            for ($i=1; $i <= 5; $i++) {
                $tenant_contract->tenantElectricityPayments()->save(factory(App\TenantElectricityPayment::class)->make([
                    'ammeter_read_date' => $ammeter_read_date->copy()->subMonth(1)->addDay($i+rand(1, $i))
                ]));
            }

            // for ($i=1; $i <= 5; $i++) {
            //     $tenant_contract->tenantElectricityPayments()->save(factory(App\TenantElectricityPayment::class)->make([
            //         'ammeter_read_date' => Carbon::now()->addDay(rand(0, $i))
            //     ]));
            // }
        });
    }
}
