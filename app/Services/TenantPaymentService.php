<?php
namespace App\Services;

use App\TenantElectricityPayment;
use App\TenantPayment;
use Carbon\Carbon;

class TenantPaymentService
{
    public static function buildTenantPaymentTableRows(Carbon $startDate, Carbon $endDate) {
        $rows = [];
        $tenantPayments = TenantPayment::whereBetween('due_time', [$startDate, $endDate])->get();
        $tenantElectricityPayments = TenantElectricityPayment::whereBetween('due_time', [$startDate, $endDate])->get();
        $payments = $tenantPayments->concat($tenantElectricityPayments)->sortByDesc('due_time');

        foreach ($payments as $payment) {
            $isTenantElectricityPayment = get_class($payment) == \App\TenantElectricityPayment::class;

            $rows[] = [
                '應繳科目ID' => '',
                '應繳科目' =>  $isTenantElectricityPayment ? '電費' : $payment->subject,
                '應繳費用' => $payment->amount,
                '應繳日期' => $payment->due_time,
                '是否已沖銷' => $payment->is_charge_off_done,
            ];
        }

        $payLogs = $tenantPayments->flatMap(function ($p) { return $p->payLogs()->get(); })
            ->concat(
                $tenantElectricityPayments->flatMap(function ($p) { return $p->payLog()->get(); })
            )
            ->sortByDesc('paid_at');

        $idx = 0;
        foreach ($payLogs as $payLog) {
            $data = [
                '繳費科目' => $payLog->subject,
                '繳費費用' => $payLog->amount,
                '繳費日期' => Carbon::parse($payLog->paid_at)->toDateString(),
                '繳納科目ID' => '',
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
}
