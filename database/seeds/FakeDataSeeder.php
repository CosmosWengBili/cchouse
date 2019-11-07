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
        'landlords', 'landlord_contracts', 'landlord_landlord_contract',
        'landlord_payments', 'landlord_other_subjects',
        'buildings', 'rooms', 'appliances', 'maintenances', 'keys', 'key_requests',
        'tenants', 'tenant_contract', 'tenant_payments', 'tenant_electricity_payments',
        'shareholders', 'building_shareholder', 'building_shareholder', 'pay_logs', 'pay_offs', 'company_incomes'
    ];

    /**
     * php artisan db:seed --class=FakeDataSeeder --env=testing
     *
     * @return void
     */
    public function run(Faker $faker)
    {
        $this->truncate();

        $now = \Carbon\Carbon::now();
        $this->year  = $now->year;
        $this->month = $now->month;

        // 先有 建築, 在對應房東契約
        factory(\App\Building::class, 2)
            ->create()
            ->each(function (\App\Building $building) {
                $building->landlordContracts()->save(factory(\App\LandlordContract::class)->make());

                $building->shareholders()->saveMany(factory(\App\Shareholder::class, rand(1, 5))->make());

                $building->rooms()->saveMany(factory(\App\Room::class, 1)->make([
                    'room_layout' => '公區'
                ]));

                $building->rooms()->saveMany(factory(\App\Room::class, rand(3, 10))->make([
                    'room_layout' => '套房'
                ]));
            });

        \App\LandlordContract::all()->each(function (\App\LandlordContract $landlord_contract) {
            $landlord_contract->landlords()->save(factory(\App\Landlord::class)->make());
        });

        \App\Room::all()->each(function (\App\Room $room) {
            // 一個房間對應一個租客契約
            $room->tenantContracts()->save(factory(\App\TenantContract::class)->make([
                'tenant_id' => factory(\App\Tenant::class)->create()->id,
            ]));

            $room->appliances()->save(factory(\App\Appliance::class)->make());

            $room->keys()->save(factory(\App\Key::class)->make());

            $room->maintenances()->saveMany(factory(\App\Maintenance::class, rand(6, 12))->make());

            //
            $room->landlordPayments()->saveMany(factory(\App\LandlordPayment::class, rand(3, 15))->make());
            $room->landlordOtherSubjects()->saveMany(factory(\App\LandlordOtherSubject::class, rand(3, 15))->make());
        });

        \App\Key::all()->each(function (\App\Key $key) {
            $key->keyRequests()->saveMany(factory(\App\KeyRequest::class, 3)->make([
                'request_user_id' => \App\User::inRandomOrder()->first(),
            ]));
        });

        \App\TenantContract::all()->each(function (\App\TenantContract $tenant_contract) use ($faker) {
            $tenant_contract->tenantPayments()->save(factory(\App\TenantPayment::class)->make([
                'subject' => '履約保證金',
                'collected_by' => '房東',
                'amount'=> $tenant_contract->deposit,
                'period'       => '次',
                'comment' => '初次履約金',
            ]));

            // faker 第一次租金
            $tenant_contract->tenantPayments()->save(factory(\App\TenantPayment::class)->make([
                'subject' => '租金',
                'period'  => '月',
            ]));

            $tenant_contract->tenantPayments()->save(factory(\App\TenantPayment::class)->make([
                'subject' => $faker->randomElement(array_slice(config('enums.tenant_payments.subject'), 1)),
            ]));

            $tenant_contract->tenantElectricityPayments()->save(factory(\App\TenantElectricityPayment::class)->make());

            // test 管理服務費
            $tenant_contract->companyIncomes()->save(factory(\App\CompanyIncome::class)->make([
                'subject' => '管理服務費',
            ]));
            // 其他收入
            $tenant_contract->companyIncomes()->save(factory(\App\CompanyIncome::class)->make([
                'subject' => $faker->randomElement(array_slice(config('enums.tenant_payments.subject'), 1)),
            ]));

            $tenant_contract->payOff()->create([
                'pay_off_type' => '協調退租'
            ]);
            $tenant_contract->payOff()->create([
                'pay_off_type' => '中途退租'
            ]);
        });

        \App\TenantPayment::all()->each(function (\App\TenantPayment $tenant_payment) use ($faker) {
            $tenant_payment->payLogs()->save(factory(\App\PayLog::class)->make([
                'tenant_contract_id' => $tenant_payment->tenant_contract_id,
                'receipt_type' => '發票',
            ]));
        });

        \App\TenantElectricityPayment::all()->each(function (\App\TenantElectricityPayment $tenant_electricity_payment) use ($faker) {
            $tenant_electricity_payment->payLogs()->save(factory(\App\PayLog::class)->make([
                'tenant_contract_id' => $tenant_electricity_payment->tenant_contract_id,
                'receipt_type' => '發票',
            ]));
        });

        // 生成 月結報告
        App\Services\ScheduleService::setMonthlyReportCarryFoward();
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

    private function truncate()
    {
        \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();

        foreach (self::SEEDER_TABLES as $table) {
            \Illuminate\Support\Facades\DB::table($table)->truncate();
        }

        \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();
    }
}
