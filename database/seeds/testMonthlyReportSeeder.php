<?php

use Illuminate\Database\Seeder;

class testMonthlyReportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = \Carbon\Carbon::now();
        $this->year  = $now->year;
        $this->month = $now->month;

        // 生成 月結報告
        \App\Services\ScheduleService::setMonthlyReportCarryFoward();
        \App\Building::all()->each(function (\App\Building $building) {
            $contract = $building->activeContracts()->first();

            if ($contract) {
                $revenue = Redis::get('monthlyRepost:carry:'.$contract->id);
                $monthlyReport = \App\MonthlyReport::create([
                            'year' => $this->year,
                            'month' => $this->month,
                            'carry_forward' => $revenue,
                            'landlord_contract_id' => $contract->id
                        ]);
            }
        });
    }
}
