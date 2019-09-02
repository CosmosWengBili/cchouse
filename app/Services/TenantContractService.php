<?php

namespace App\Services;

use App\TenantContract;
use App\TenantPayment;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

use App\Services\PeriodService;

class TenantContractService
{
    public function __construct(PeriodService $periodService)
    {
        $this->periodService = $periodService;
    }

    // things to do when creating a new tenant contract
    // 1. create tenant contract
    // 2. generate payments
    public function create($data, $payments = [])
    {
        $tenantContract = DB::transaction(function () use ($data, $payments) {
            // create tenant contract
            $tenantContractId = TenantContract::insertGetId($data);
            $tenantContract = TenantContract::find($tenantContractId);

            // by default, each tenant contract has one rent payment per month
            $payments[] = [
                'subject' => '租金',
                'period' => '月',
                'amount' => $tenantContract->rent,
                'collected_by' => '公司'
            ];

            // generates all kinds of payments
            foreach ($payments as $payment) {
                // get the dates that this payment shoud be collected
                // make a payment instance for it
                $futurePayments = $this->periodService->every(
                    $payment['period'],
                    $tenantContract->contract_start,
                    $tenantContract->contract_end,
                    function ($date) use ($payment) {
                        return TenantPayment::make([
                            'subject' => $payment['subject'],
                            'due_time' => $date,
                            'amount' => $payment['amount'],
                            'collected_by' => $payment['collected_by'],
                            'period' => $payment['period'],
                        ]);
                    }
                );

                // link these payments to the newly created tenant contract
                $tenantContract->tenantPayments()->saveMany($futurePayments);
            }

            return $tenantContract;
        });

        return $tenantContract;
    }

    // make a temporary extended tenant contract, by default it lasts for one month
    public function makeExtendedContract(
        TenantContract $oldTenantContract,
        $months = 1
    ) {
        $endedAt = Carbon::create($oldTenantContract->contract_end);
        $newStart = $endedAt->copy()->addDay();

        $newTenantContract = $oldTenantContract->replicate(['deleted_at']);
        $newTenantContract->contract_serial_number = '';
        $newTenantContract->contract_start = $newStart->format('Y-m-d');
        $newTenantContract->contract_end = $newStart
            ->copy()
            ->addMonthsWithoutOverflow($months)
            ->format('Y-m-d');
        return $newTenantContract;
    }
}
