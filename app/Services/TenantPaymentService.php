<?php
namespace App\Services;

use App\TenantContract;
use App\TenantElectricityPayment;
use App\TenantPayment;
use Carbon\Carbon;

class TenantPaymentService
{
    public static function buildTenantPaymentTableRows(string $roomCode, string $tenantName, Carbon $startDate, Carbon $endDate) {
        $tenantContractIds = self::findTenantContractIdsBy($roomCode, $tenantName);
        $rows = [];
        $tenantPayments = TenantPayment::whereIn('tenant_contract_id', $tenantContractIds)
                            ->whereBetween('due_time', [$startDate, $endDate])->get();
        $tenantElectricityPayments = TenantElectricityPayment::whereIn('tenant_contract_id', $tenantContractIds)
                                        ->whereBetween('due_time', [$startDate, $endDate])->get();
        $payments = $tenantPayments->concat($tenantElectricityPayments)->sortByDesc('due_time');

        foreach ($payments as $payment) {

            $rows[] = [
                '應繳科目編號' => $payment->id,
                '應繳科目' =>  $payment->subject,
                '應繳費用' => $payment->amount,
                '應繳日期' => $payment->due_time,
                '是否已沖銷' => $payment->is_charge_off_done,
            ];
        }

        $payLogs = $tenantPayments->flatMap(function ($p) { return $p->payLogs()->get(); })
            ->concat(
                $tenantElectricityPayments->flatMap(function ($p) { return $p->payLogs()->get(); })
            )
            ->sortByDesc('paid_at');

        $idx = 0;
        foreach ($payLogs as $payLog) {
            $data = [
                '繳費科目' => $payLog->subject,
                '繳費費用' => $payLog->amount,
                '繳費日期' => Carbon::parse($payLog->paid_at)->toDateString(),
                '繳納科目編號' => $payLog->loggable->id,
            ];

            if(isset($rows[$idx])) {
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
        return TenantContract::join('rooms', 'rooms.id', '=', "tenant_contract.room_id")
            ->join('tenants', 'tenants.id', '=', "tenant_contract.tenant_id")
            ->where('rooms.room_code', $roomCode)
            ->where('tenants.name', $tenantName)
            ->select('tenant_contract.id')
            ->pluck('tenant_contract.id');
    }
}
