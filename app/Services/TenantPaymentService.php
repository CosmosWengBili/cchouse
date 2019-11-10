<?php

namespace App\Services;

use App\SystemVariable;
use App\TenantContract;
use App\TenantElectricityPayment;
use App\TenantPayment;
use Carbon\Carbon;

class TenantPaymentService
{
    public static function buildTenantPaymentTableRows(?string $roomCode, ?string $tenantName, ?Carbon $startDate, ?Carbon $endDate)
    {
        $rows = [];
        $subjects = SystemVariable::where('group', 'Reversal')->orderBy('order', 'desc')->pluck('code'); // order 小的在後面
        $tenantContractIds = self::findTenantContractIdsBy($roomCode, $tenantName);

        $tenantPayments = TenantPayment::whereIn('tenant_contract_id', $tenantContractIds);
        $tenantElectricityPayments = TenantElectricityPayment::whereIn('tenant_contract_id', $tenantContractIds);
        if (!is_null($startDate)) {
            $tenantPayments = $tenantPayments->where('due_time', '>=', $startDate);
            $tenantElectricityPayments = $tenantElectricityPayments->where('due_time', '>=', $startDate);
        }
        if (!is_null($endDate)) {
            $tenantPayments = $tenantPayments->where('due_time', '<=', $endDate);
            $tenantElectricityPayments = $tenantElectricityPayments->where('due_time', '<=', $endDate);
        }
        $tenantPayments = $tenantPayments->get();
        $tenantElectricityPayments = $tenantElectricityPayments->get();

        // 照 應繳日期 再照 科目排序
        $payments = $tenantPayments->concat($tenantElectricityPayments)
            ->sortBy(function ($tp) use ($subjects) {
                // 資料按 `應繳日期` 再按 `沖銷順序` 排序
                return $tp->due_time . '#' . ($subjects->search($tp->subject) + 10);
            });

        foreach ($payments as $payment) {
            $rows[] = [
                '應繳科目' => $payment->subject,
                '應繳費用' => $payment->amount,
                '應繳日期' => $payment->due_time->toDateString(),
                '是否已沖銷' => $payment->is_charge_off_done,
            ];
        }

        $payLogs = $tenantPayments->flatMap(function ($p) {
            return $p->payLogs()->get();
        })
            ->concat(
                $tenantElectricityPayments->flatMap(function ($p) {
                    return $p->payLogs()->get();
                })
            )
            ->sortByDesc('paid_at');

        $idx = 0;
        foreach ($payLogs as $payLog) {
            $data = [
                '繳費科目' => $payLog->subject,
                '繳費費用' => $payLog->amount,
                '繳費應繳日期' => $payLog->loggable->due_time->toDateString(),
                '繳費日期' => Carbon::parse($payLog->paid_at)->toDateString(),
            ];

            if (isset($rows[$idx])) {
                $rows[$idx] = array_merge($rows[$idx], $data);
            } else {
                $rows[] = $data;
            }
            $idx++;
        }

        return $rows;
    }


    private static function findTenantContractIdsBy($roomCode, $tenantName)
    {
        $relation = TenantContract::select('tenant_contract.id');
        if (!is_null($roomCode)) {
            $relation = $relation->join('rooms', 'rooms.id', '=', "tenant_contract.room_id")
                ->where('rooms.room_code', $roomCode);
        }
        if (!is_null($tenantName)) {
            $relation = $relation->join('tenants', 'tenants.id', '=', "tenant_contract.tenant_id")
                ->where('tenants.name', $tenantName);
        }

        return $relation->pluck('tenant_contract.id');
    }
}
