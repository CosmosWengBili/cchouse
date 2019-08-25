<?php
namespace App\Services;

use App\TenantContract;
use App\TenantElectricityPayment;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class PayOffService
{
    private $lastPayDate;
    private $payOffDate;
    private $tenantContract;
    private $comment;

    function __construct(Carbon $payOffDate, TenantContract $tenantContract) {
        $this->payOffDate = $payOffDate->endOfDay();
        $this->tenantContract = $tenantContract;
        $this->lastPayDate = $this->buildLastPayDate();
        $this->comment = $this->buildComment();
    }

    public function buildPayOffData() {
        $electricityPayment = $this->lastTenantElectricityPayment();
        $payments = $this->lastTenantPayments();
        $endDegreeOf110v = $electricityPayment['110v_end_degree'];
        $endDegreeOf220v = $electricityPayment['220v_end_degree'];
        $depositPaid = $this->tenantContract->deposit_paid;
        $fees = [
            ['subject' => '履保金', 'amount' => $depositPaid, 'comment' => ''],
        ];

        // 未繳納的電費
        if(!$electricityPayment->is_charge_off_done) {
            $fees[] = ['subject' => '電費', 'amount' => -($electricityPayment->amount), 'comment' => ''];
        }

        // 房租雜費
        $fees = array_merge($fees, $this->buildPaymentFees($payments));

        return [
            '110v_end_degree' => $endDegreeOf110v,
            '220v_end_degree' => $endDegreeOf220v,
            'fees' => $fees,
        ];
    }

    /**
     * 最後一期電費
     */
    private function lastTenantElectricityPayment() {
        return $this->tenantContract
                    ->tenantElectricityPayments()
                    ->orderBy('due_time', 'desc')
                    ->first();
    }

    /**
     * 上個租金支付日 至 點收當日 的應繳費用
     */
    private function lastTenantPayments() {
        return $this->tenantContract
                    ->tenantPayments()
                    ->whereBetween('due_time', [$this->lastPayDate, $this->payOffDate])
                    ->get();
    }

    /**
     * 計算上個租金支付日
     */
    private function buildLastPayDate(): Carbon {
        $rentPayDay = $this->tenantContract->rent_pay_day;

        $result = $this->payOffDate->copy()->startOfDay();
        if ($result->day < $rentPayDay) {
            $result->subMonth(1);
        }

        return $result->setDay($rentPayDay);
    }

    /**
     * 組費用 comment
     *
     * example:
     *   "合約 09/05 到期，08/25 點交"
     */
    private function buildComment() {
        $contractEnd = $this->tenantContract->contract_end->format('m/d');
        $payOffDate = $this->payOffDate->format('m/d');

        return "合約 ${contractEnd} 到期，${payOffDate} 點交。";
    }

    /**
     * @param Collection $payments
     * @return array
     */
    private function buildPaymentFees(Collection $payments): array
    {
        // 差多少日合約到期
        $diffInDays = $this->payOffDate->copy()->startOfDay()->diffInDays($this->tenantContract->contract_end);
        $fees = [];
        foreach ($payments as $payment) {
            $subject = $payment->subject;
            $amount = $payment->amount;
            $isPayOff = $payment->is_pay_off;
            $isChargeOffDone = $payment->is_charge_off_done;

            if ($isPayOff) {
                $fees[] = ['subject' => $subject, 'amount' => -$amount, 'comment' => $payment->comment];
            } else {
                if ($isChargeOffDone) {
                    $amount = round($amount * $diffInDays / 30.0);
                } else {
                    $amount = round(-($amount) * (30 - $diffInDays) / 30.0);
                }

                $fees[] = ['subject' => $subject, 'amount' => $amount, 'comment' => $this->comment];
            }
        }
        return $fees;
    }
}
