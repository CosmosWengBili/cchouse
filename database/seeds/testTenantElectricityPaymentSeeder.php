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

        $room = App\Room::find(48);
        $room->tenantContracts()->saveMany(factory(App\TenantContract::class, 3)->make([
            'contract_start' => $start_date,
            'contract_end' => $end_date,
        ]))->each(function ($tenant_contract) {
            $contract_start = $tenant_contract->contract_start;

            for ($i=1; $i <= 10; $i++) {
                $tenant_contract->tenantElectricityPayments()->save(factory(App\TenantElectricityPayment::class)->make([
                    'ammeter_read_date' => Carbon::parse($contract_start)->addDay($i)
                ]));
            }
        });
    }
}
