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
            // insertGetId does not support auto created_at
            $data['created_at'] = $data['updated_at'] = Carbon::now();

            // create tenant contract
            $tenantContractId = TenantContract::insertGetId($data);
            $tenantContract = TenantContract::find($tenantContractId);

            $room = $tenantContract->room;
            $room->update(['room_status' => '已出租']);
            $room->deposits()->update(['is_deposit_collected' => true]);

            // by default, each tenant contract has one rent payment per month
            $payments[] = [
                'subject'      => '租金',
                'period'       => '月',
                'amount'       => $tenantContract->rent,
                'collected_by' => '房東'
            ];

            $payments[] = [
                'subject'      => '履約保證金',
                'period'       => '次',
                'amount'       => $tenantContract->deposit,
                'collected_by' => '房東'
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
                            'subject'              => $payment['subject'],
                            'due_time'             => $date,
                            'amount'               => $payment['amount'],
                            'collected_by'         => $payment['collected_by'],
                            'period'               => $payment['period'],
                            'is_visible_at_report' => true
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
        $endedAt  = Carbon::create($oldTenantContract->contract_end->format('Y-m-d'));
        $newStart = $endedAt->copy()->addDay();

        $newTenantContract                         = $oldTenantContract->replicate(['deleted_at']);
        $newTenantContract->contract_serial_number = '';
        $newTenantContract->contract_start         = $newStart->format('Y-m-d');
        $newTenantContract->contract_end           = $newStart
                                                        ->copy()
                                                        ->addMonthsWithoutOverflow($months)
                                                        ->format('Y-m-d');

        $newTenantContract->rent                = '';
        $newTenantContract->deposit             = '';
        $newTenantContract->deposit_paid        = '';
        $newTenantContract['110v_start_degree'] = '';
        $newTenantContract['220v_start_degree'] = '';
        $newTenantContract['110v_end_degree']   = '';
        $newTenantContract['220v_end_degree']   = '';
        $newTenantContract->old_tenant_contract_id = $oldTenantContract->id;

        return $newTenantContract;
    }
}
