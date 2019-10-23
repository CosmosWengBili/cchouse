<?php
namespace App\Services;

use App\DebtCollection;
use App\TenantContract;
use App\TenantPayment;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class PayOffService
{
    private $lastPayDate;
    private $payOffDate;
    /** @var TenantContract $tenantContract */
    private $tenantContract;
    private $comment;
    private $returnWay;

    function __construct(Carbon $payOffDate, TenantContract $tenantContract, $returnWay)
    {
        $this->payOffDate = $payOffDate->endOfDay();
        $this->tenantContract = $tenantContract;
        $this->lastPayDate = $this->buildLastPayDate();
        $this->comment = $this->buildComment();
        $this->returnWay = $returnWay;
    }

    public function buildPayOffData()
    {
        /*
         * $payDiffDays 兩個租金支付日的差異天數
         * $lastPaiedUntilToday 上一個租金支付日到今天的差異天數
         */
        list($payDiffDays, $lastPayedUntilTodayDiffDays) = $this->getDiffDays();

        $diffDays = $lastPayedUntilTodayDiffDays / $payDiffDays;// 天數差異
        $electricityPayment = $this->lastTenantElectricityPayment();
        $payments = $this->lastTenantPayments();
        $endDegreeOf110v = $electricityPayment['110v_end_degree'];
        $endDegreeOf220v = $electricityPayment['220v_end_degree'];
        $depositPaid = $this->tenantContract->deposit_paid; // 押金已繳納

        // 1. 履保金
        $fees[] = ['subject' => '履保金', 'amount' => $depositPaid, 'comment' => ''];

        // 2. 未繳納的電費
        if ($electricityPayment && !$electricityPayment->is_charge_off_done) {
            $fees[] = ['subject' => '電費', 'amount' => -($electricityPayment->amount), 'comment' => ''];
        }

        // 3. 滯納金
        $fines = $this->getOverDueFines();

        // 4. 其他繳納的項目
        $others = $this->buildPaymentFees($payments, $payDiffDays, $lastPayedUntilTodayDiffDays, $depositPaid);

        $fines_others = array_merge($fines, $others);

        // 5. 整理 $others 追加屬性
        $others = $this->handleFees($fines_others, $payDiffDays, $lastPayedUntilTodayDiffDays, $depositPaid);

        // 6. merge
        $fees = array_merge($fees, $others, $fines);

        // 7. 最後根據不同的承租方式與退租方式 追加屬性
        $withdrawal_revenue_distribution = $this->tenantContract->building->landlordContracts()->active()->withdrawal_revenue_distribution;
        list($defaultItems, $sumItems) = $this->regenerateItems($fees, $diffDays, $withdrawal_revenue_distribution);

        return [
            'withdrawal_revenue_distribution' => $withdrawal_revenue_distribution,
            '110v_end_degree' => $endDegreeOf110v,
            '220v_end_degree' => $endDegreeOf220v,
            'fees' => $defaultItems,
            'sums' =>  $sumItems,
        ];
    }

    /**
     * 根據excel表 進行各個科目與總合的計算
     * @param array $fees
     * @param       $diffDays
     *
     * @return array
     */
    private function regenerateItems(array $fees, $diffDays, $withdrawal_revenue_distribution)
    {
        $commission_type = $this->tenantContract->building->landlordContracts()->active()->commission_type;
        $return_ways = request()->input('return_ways', '中途退租');

        $sumItems = [
            '應退金額' => 0,
            '兆基應收' => 0,
            '業主應付' => 0,
        ];

        $defaultItems = [
            '履保金' => ['subject' => '履保金', 'amount' => 0, 'comment' => '', 'is_showed' => false],
            '管理費' => ['subject' => '管理費', 'amount' => 0, 'comment' => '', 'is_showed' => false],
            '折抵管理費' => ['subject' => '折抵管理費', 'amount' => 0, 'comment' => '', 'is_showed' => false],
            '清潔費' => ['subject' => '清潔費', 'amount' => 0, 'comment' => '', 'is_showed' => false],
            '折抵清潔費' => ['subject' => '折抵清潔費', 'amount' => 0, 'comment' => '', 'is_showed' => false],
            '滯納金' => ['subject' => '滯納金', 'amount' => 0, 'comment' => '', 'is_showed' => false],
            '折抵滯納金' => ['subject' => '折抵滯納金', 'amount' => 0, 'comment' => '', 'is_showed' => false],
            '沒收押金' => ['subject' => '沒收押金', 'amount' => 0, 'comment' => '', 'is_showed' => false],
            '點交中退盈餘分配' => ['subject' => '點交中退盈餘分配', 'amount' => 0, 'comment' => '', 'is_showed' => false],
            '租金' => ['subject' => '租金', 'amount' => 0, 'comment' => '', 'is_showed' => false],
            '電費' => ['subject' => '電費', 'amount' => 0, 'comment' => '', 'is_showed' => false],
        ];

        // 整理成 excel 內的格式
        foreach ($fees as $fee) {
            if( array_key_exists($fee['subject'], $defaultItems) ){
                $defaultItems[$fee['subject']] = array_merge($defaultItems[$fee['subject']], $fee);
            }
            else{
                $defaultItems[$fee['subject']] = [
                    'subject' => $fee['subject'],
                    'amount' => $fee['amount'],
                    'comment' => $fee['comment'],
                    'is_showed' => true
                ];                
            }
            
        }

        if ($commission_type === '包租' && $return_ways === '中途退租') {
            $defaultItems['履保金']['is_showed'] = true;
            $defaultItems['滯納金']['is_showed'] = true;
            $defaultItems['折抵滯納金']['is_showed'] = true;
            $defaultItems['點交中退盈餘分配']['is_showed'] = true;
            $defaultItems['沒收押金']['is_showed'] = true;
            $defaultItems['點交中退盈餘分配']['is_showed'] = true;

            // -(履保金+管理費+清潔費+設備+滯納金)
            $defaultItems['沒收押金']['amount'] = (
                    $defaultItems['履保金']['amount'] +
                    $defaultItems['管理費']['amount'] +
                    $defaultItems['清潔費']['amount'] +
                    $defaultItems['滯納金']['amount']
                ) * -1;
            // ( 沒收押金 * -1 * ( 1 - landlordContract - withdrawal_revenue_distribution ) )
            $defaultItems['點交中退盈餘分配']['amount'] = $defaultItems['沒收押金']['amount'] * -1 * (1 - $withdrawal_revenue_distribution);

            $sumItems['兆基應收'] = $defaultItems['履保金']['amount'];

            // −1×(IF(B16>0,0,B16)+IF(B19>0,0,B19))+B22
            $sumItems['業主應付'] = -1 * ($defaultItems['清潔費']['amount'] > 0 ? 0 : $defaultItems['清潔費']['amount'])
                + ($defaultItems['滯納金']['amount'] > 0 ? 0 : $defaultItems['滯納金']['amount'])
                + $defaultItems['點交中退盈餘分配']['amount'];
        } elseif ($commission_type === '包租' && $return_ways === '到期退租') {
            $defaultItems['履保金']['is_showed'] = true;
            $defaultItems['租金']['is_showed'] = true;
            $defaultItems['滯納金']['is_showed'] = true;

            // 租金
            $rent = $this->tenantContract->rent;
            $rent_pay_log = $this->lastTenantPayments()->where('subject', '租金')->sum('amount');
            // ROUND($C5−$B5×$G2,0)
            $defaultItems['租金']['amount'] = $rent_pay_log - ($rent * $diffDays);
            // −1×(B52+B54)+B56+B51
            $sumItems['業主應付'] = -1 * ($defaultItems['清潔費']['amount'] + $defaultItems['滯納金']['amount']) +
                $defaultItems['管理費']['amount'] +
                $sumItems['應退金額'];
            // SUM(B49:B50)+SUM(B52:B54)
            $sumItems['應退金額'] = $defaultItems['履保金']['amount'] +
                $defaultItems['租金']['amount'] +
                $defaultItems['清潔費']['amount'] +
                $defaultItems['滯納金']['amount'];
            // B49−B56
            $sumItems['兆基應收'] = $defaultItems['履保金']['amount'] - $sumItems['應退金額'];
        } elseif ($commission_type === '包租' && $return_ways === '協調退租') {
            $defaultItems['履保金']['is_showed'] = true;
            $defaultItems['沒收押金']['is_showed'] = true;
            $defaultItems['租金']['is_showed'] = true;
            $defaultItems['滯納金']['is_showed'] = true; // 不顯示在月結單 ????
            $defaultItems['點交中退盈餘分配']['is_showed'] = true;

            // 租金
            $rent = $this->tenantContract->rent;
            $rent_pay_log = $this->lastTenantPayments()->where('subject', '租金')->sum('amount');
            $defaultItems['租金']['amount'] = $rent_pay_log - ($rent * $diffDays);
            $defaultItems['點交中退盈餘分配']['amount'] = $defaultItems['沒收押金']['amount'] * -1 * (1 - $withdrawal_revenue_distribution);
            // SUM(B32:B34)+SUM(B36:B38)
            $sumItems['應退金額'] = $defaultItems['履保金']['amount'] +
                $defaultItems['沒收押金']['amount'] +
                $defaultItems['租金']['amount'];
            // B32−B41
            $sumItems['兆基應收'] = $defaultItems['履保金']['amount'] - $sumItems['應退金額']['amount'];
            // B41+B35+(B36+B38)×−1+B39
            $sumItems['業主應付'] = $sumItems['應退金額'] +
                $defaultItems['管理費']['amount'] +
                ($defaultItems['清潔費']['amount'] + $defaultItems['滯納金']['amount']) * -1 +
                $defaultItems['點交中退盈餘分配']['amount'];
        } elseif ($commission_type === '代管' && $return_ways === '中途退租') {
            $defaultItems['履保金']['is_showed'] = true;
            $defaultItems['滯納金']['is_showed'] = true;
            $defaultItems['折抵滯納金']['is_showed'] = true;
            $defaultItems['沒收押金']['is_showed'] = true;
            $defaultItems['點交中退盈餘分配']['is_showed'] = true;

            // −1×(E13+E14+E16+E18+E19)
            $defaultItems['沒收押金']['amount'] = (
                    $defaultItems['履保金']['amount'] +
                    $defaultItems['管理費']['amount'] +
                    $defaultItems['清潔費']['amount'] +
                    $defaultItems['滯納金']['amount']
                ) * -1;
            $defaultItems['點交中退盈餘分配']['amount'] = $defaultItems['沒收押金']['amount'] * -1 * (1 - $withdrawal_revenue_distribution);
            // −1×(IF(B16>0,0,B16)+IF(B19>0,0,B19))+B22
            $sumItems['兆基應收'] = $sumItems['業主應付'] = -1 *
                ($defaultItems['清潔費']['amount'] > 0 ? 0 : $defaultItems['清潔費']['amount']) +
                ($defaultItems['滯納金']['amount'] > 0 ? 0 : $defaultItems['滯納金']['amount']) +
                $defaultItems['點交中退盈餘分配']['amount'];
        } elseif ($commission_type === '代管' && $return_ways === '到期退租') {
            $defaultItems['履保金']['is_showed'] = true;
            $defaultItems['租金']['is_showed'] = true;
            $defaultItems['滯納金']['is_showed'] = true;

            // 租金
            $rent = $this->tenantContract->rent;
            $rent_pay_log = $this->lastTenantPayments()->where('subject', '租金')->sum('amount');
            // ROUND($C5−$B5×$G2,0)
            $defaultItems['租金']['amount'] = $rent_pay_log - ($rent * $diffDays);
            // SUM(E49:E50)+SUM(E52:E54)
            $sumItems['應退金額'] = $defaultItems['履保金']['amount'] +
                $defaultItems['租金']['amount'] +
                $defaultItems['清潔費']['amount'] +
                $defaultItems['滯納金']['amount'];
            // −1×(E54+E52)+E51
            $sumItems['兆基應收'] = -1 * ($defaultItems['清潔費']['amount'] + $defaultItems['滯納金']['amount']) +
                $defaultItems['管理費']['amount'];
            // −1×(B52+B54)+B56+B51
            $sumItems['業主應付'] = -1 * ($defaultItems['清潔費']['amount'] + $defaultItems['滯納金']['amount']) +
                $defaultItems['管理費']['amount'] +
                $sumItems['應退金額'];
        } elseif ($commission_type === '代管' && $return_ways === '協調退租') {
            $defaultItems['履保金']['is_showed'] = true;
            $defaultItems['沒收押金']['is_showed'] = true;
            $defaultItems['租金']['is_showed'] = true;
            $defaultItems['滯納金']['is_showed'] = true;
            $defaultItems['點交中退盈餘分配']['is_showed'] = true;

            // 租金
            $rent = $this->tenantContract->rent;
            $rent_pay_log = $this->lastTenantPayments()->where('subject', '租金')->sum('amount');
            // −E32÷2
            $defaultItems['沒收押金']['amount'] = -1 * $defaultItems['履保金']['amount'] / 2;
            $defaultItems['租金']['amount'] = $rent_pay_log - ($rent * $diffDays);
            $defaultItems['點交中退盈餘分配']['amount'] = $defaultItems['沒收押金']['amount'] * -1 * (1 - $withdrawal_revenue_distribution);

            // SUM(E32:E34)+SUM(E36:E38)
            $sumItems['應退金額'] = $defaultItems['履保金']['amount'] +
                $defaultItems['沒收押金']['amount'] +
                $defaultItems['租金']['amount'] +
                $defaultItems['清潔費']['amount'] +
                $defaultItems['滯納金']['amount'];
            // E39+−1×(E36+E38)+E35
            $sumItems['兆基應收'] = $defaultItems['點交中退盈餘分配']['amount'] +
                -1 * ($defaultItems['清潔費']['amount'] + $defaultItems['滯納金']['amount']) +
                $defaultItems['管理費']['amount'];
            // E41+E35+(E36+E38)×−1+E39
            $sumItems['業主應付'] = $sumItems['應退金額'] +
                $defaultItems['管理費']['amount'] +
                ($defaultItems['清潔費']['amount'] + $defaultItems['滯納金']['amount']) * -1 +
                $defaultItems['點交中退盈餘分配']['amount'];
        }

        // do round for all items
        $defaultItems = collect($defaultItems)->map(function ($item, $key) {
            isset($item['amount']) and ($item['amount'] = round($item['amount']));
            return $item;
        })->toArray();
        $sumItems = collect($sumItems)->map(function ($item, $key) {
            return round($item);
        })->toArray();

        return [
            $defaultItems,
            $sumItems,
        ];
    }

    /**
     * @param array $others
     * @param int   $diffInDays 兩個租金支付日的差異天數
     * @param int   $lastPayedUntilToday 上一個租金支付日到今天的差異天數
     * @param int   $depositPaid 押金已繳納
     *
     * @return array
     */
    private function handleFees(array $others, int $diffInDays, int $lastPayedUntilToday, int $depositPaid)
    {
        $data = [];
        foreach ($others as $fee) {
            $copy = [];
            $fee['is_showed'] = true;

            if ($fee['subject'] === '管理費') {
                // 計算管理費
                $fee['amount'] = $this->managementFee($diffInDays, $lastPayedUntilToday);

                $copy = $fee;
                $copy['subject'] = '折抵管理費';
                if ($fee['amount'] === 0) {
                    $fee['is_showed'] = $copy['is_showed'] = false; // 0 不顯示
                } else {
                    $copy['amount'] = $copy['amount'] * -1;
                }
            } elseif ($fee['subject'] === '清潔費') {
                $copy = $fee;
                $copy['subject'] = '折抵清潔費';
                $copy['amount'] = $fee['amount'] * -1;
                if ($fee['amount'] === 0) {
                    $fee['is_showed'] = $copy['is_showed'] = false;
                } else if($this->returnWay != '中途退租'){
                    $copy['is_showed'] = false;
                }
            } elseif ($fee['subject'] === '滯納金') {
                $copy = $fee;
                $copy['subject'] = '折抵滯納金';
                $copy['amount'] = $fee['amount'] * -1;
            }

            $data[] = $fee;
            ! empty($copy) and $data[] = $copy;
        }

        return $data;
    }

    /**
     * 計算管理費
     * @param $diffInDays
     * @param $lastPayedUntilToday
     *
     * @return float
     */
    private function managementFee($diffInDays, $lastPayedUntilToday)
    {
        // IF($C5−($B5×$G2)>0,0,ROUND(($C5−($B5×$G2))×0.1,0))
        $managementFee = 0;
        // 租金
        $rent = $this->tenantContract->rent;
        // 天數差異
        $percentage = $lastPayedUntilToday / $diffInDays;
        $payments = $this->lastTenantPayments();
        $payments = $this->sumPayLogsByPayments($payments);

        foreach ($payments as $payment) {
            if ($payment['subject'] === '管理費') {
                // 沖銷金額
                $pay_log_amount = $payment['pay_log_amount'];
                if (($pay_log_amount - $rent * $percentage) > 0) {
                    $managementFee = 0;
                    break;
                } else {
                    $room = $this->tenantContract->room;
                    // 不論管理費的計算模式 都需要按照天數差異做計算 假設管理費一個月一千 這個月30天只住了15天 則管理費應該是五百
                    $managementFee = 0;
                    if ($room->management_fee_mode === '比例') {
                        $managementFee = $pay_log_amount - ($rent * $percentage) * ( ($room->management_fee / 100) * $percentage);
                    } elseif ($room->management_fee_mode === '固定') {
                        $managementFee = $pay_log_amount - ($rent * $percentage) - ($room->management_fee * $percentage);
                    }
                }
            }
        }

        return $managementFee;
    }

    // 根據每個 payment 對應到的 pay logs 把 pay logs 的沖銷金額(amount)加起來
    private function sumPayLogsByPayments(Collection $payments): array
    {
        return $payments->map(function (TenantPayment $payment, $key) {
            $data = $payment->toArray();
            // 沖銷金額
            $data['pay_log_amount'] = $payment->payLogs->sum('amount');

            return $data;
        })
            ->toArray();
    }

    /**
     * @return array
     */
    private function getDiffDays()
    {
        // 取得上個租金支付日
        $last = $this->buildLastPayDate();
        // 取得下個租金支付日
        $next = $last->copy()->addMonth();

        $today = Carbon::today();
        // 回傳差異日
        return [
            // 租金支付日之間的差異天數
            $next->diffInDays($last, true),
            // 租金支付日到今天的差異天數
            $today->diffInDays($last, true) + 1,
        ];
    }

    /**
     * 取得滯納金
     * 在這裡將滯納金多筆會整合為一筆 並且將備註整合一起
     * @return array
     */
    private function getOverDueFines()
    {
        $rowFines = DebtCollection::where([
            'tenant_contract_id' => $this->tenantContract->id,
            'is_penalty_collected' => 1, // 是否收滯納金
        ])
            ->get()
            ->map(function ($item, $key) {
                return [
                    'subject' => '滯納金',
                    'amount' => -$this->tenantContract->overdue_fine,
                    'comment' => $this->tenantContract->comment,
                ];
            })
            ->toArray();
        $fine = [
            'subject' => '滯納金',
            'amount' => 0,
            'comment' => '',
        ];
        $tmpComment = [];
        foreach ($rowFines as $rowFine) {
            $fine['amount'] += $rowFine['amount'];
            if ($rowFine['comment'] !== '') {
                $tmpComment[] = $rowFine['comment'];
            }
        }

        if (! empty($tmpComment)) {
            $fine['comment'] = implode('<br>', $tmpComment);
        }

        return [$fine];
    }

    /**
     * 最後一期電費
     */
    private function lastTenantElectricityPayment()
    {
        return $this->tenantContract
                    ->tenantElectricityPayments()
                    ->orderBy('due_time', 'desc')
                    ->first();
    }

    /**
     * 上個租金支付日 至 點收當日 的應繳費用
     */
    private function lastTenantPayments()
    {
        return $this->tenantContract
                    ->tenantPayments()
                    ->whereBetween('due_time', [$this->lastPayDate, $this->payOffDate])
                    ->get();
    }

    /**
     * 計算上個租金支付日
     */
    private function buildLastPayDate(): Carbon
    {
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
    private function buildComment()
    {
        $contractEnd = $this->tenantContract->contract_end;
        $payOffDate = $this->payOffDate->format('m/d');

        return "合約 ${contractEnd} 到期，${payOffDate} 點交。";
    }

    /**
     * @param Collection $payments
     * @param int        $diffInDays
     * @param int        $lastPayedUntilToday
     * @param int        $depositPaid
     *
     * @return array
     */
    private function buildPaymentFees(Collection $payments, int $diffInDays, int $lastPayedUntilToday, int $depositPaid): array
    {
        $fees = [];
        $cleanFee = [
            'subject' => '清潔費',
            'amount' => 0,
            'comment' => '',
        ];
        foreach ($payments as $payment) {
            $subject = $payment->subject;
            $amount = $payment->amount;
            $isPayOff = $payment->is_pay_off;
            $isChargeOffDone = $payment->is_charge_off_done;

            if ($isPayOff) {
                $fee = ['subject' => $subject, 'amount' => -$amount, 'comment' => $payment->comment, 'collected_by' => $payment->collected_by];
            } else {
                if ($isChargeOffDone) {
                    $amount = $amount * ($lastPayedUntilToday / $diffInDays);
                } else {
                    $amount = -($amount) * ($lastPayedUntilToday - $diffInDays) / $lastPayedUntilToday;
                }

                $fee = ['subject' => $subject, 'amount' => $amount, 'comment' => $this->comment, 'collected_by' => $payment->collected_by];
            }


            //// 計算清潔費
            // 沖銷金額
            $pay_log_amount = $payment->payLogs->sum('amount');
            // 天數差異
            $percentage = $lastPayedUntilToday / $diffInDays;
            if ($fee['collected_by'] === '公司') {
                // 只要是來自公司的帳 都計算成清潔費
                $cleanFee['amount'] += $pay_log_amount - ($payment->amount * $percentage);
            } else {
                $fees[] = $fee;
            }
            //// END 計算清潔費
        }

        $fees[] = $cleanFee;

        return $fees;
    }
}
