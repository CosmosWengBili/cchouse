<?php

namespace App\Listeners;

use App\Events\ReceivableArrived;
use App\ReversalErrorCase;
use App\TenantContract;
use App\TenantElectricityPayment;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\TenantPayment;
use App\PayLog;
use App\LandlordOtherSubject;
use App\SystemVariable;
use Illuminate\Support\Facades\DB;
use App\Services\PeriodService;
use Carbon\Carbon;
use phpDocumentor\Reflection\Types\Integer;

class ReverseTenantPayments
{
    protected $periodService;
    protected $order;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(PeriodService $periodService)
    {
        $this->periodService = $periodService;
        $this->order = SystemVariable::ofGroup('Reversal')->pluck('code');
    }

    /**
     * Handle the event.
     *
     * @param  ReceivableArrived  $event
     * @return void
     */
    public function handle(ReceivableArrived $event)
    {
        $result = [
            'success' => false,
            'message' => ''
        ];
        $amount = intval($event->data['amount']);
        $virtualAccount = $event->data['virtual_account'];
        $paidAt = $event->data['txTime'];
        $tenantContract = $event->tenantContract;

        $res = DB::transaction(function () use(
            $tenantContract, $virtualAccount, $paidAt, $amount
        ) {
            $targetContract = $tenantContract;
            $restAmount = $amount;

            while ($targetContract) {
                $restAmountAfterReverse = $this->reverse($targetContract, $virtualAccount, $paidAt, $restAmount);
                $amountOfThisReverse = $restAmount - $restAmountAfterReverse;
                $this->recordSumPaid($targetContract, $amountOfThisReverse); // 紀錄「已繳總額」

                $restAmount = $restAmountAfterReverse;
                $targetContract = $targetContract->nextTenantContract();
            }

            if($restAmount > 0) {
                $payLogData = [
                    'subject'            => '溢繳無法沖銷費用',
                    'payment_type'       => '租金雜費',
                    'virtual_account'    => $virtualAccount,
                    'paid_at'            => $paidAt,
                    'amount'             => $restAmount,
                    'tenant_contract_id' => $tenantContract ? $tenantContract->id : null,
                    'loggable_type'      => 'OverPayment',
                    'loggable_id'        => 0, // 0 為溢繳費用（不關連至任何 TenantPayment 或 TenantElectricityPayment)
                ];
                $payLog = PayLog::create($payLogData);

                if (is_null($tenantContract)) {
                    $this->createReversalErrorCase('無合約入帳', $payLog);
                } else {
                    $this->createReversalErrorCase('無續約之溢繳入帳', $payLog);
                }
            }


            return true;
        });

        $result['success'] = $res;

        return $result;
    }

    // 自動沖銷
    private function reverse($tenantContract, $virtualAccount, $paidAt, $amount) {
        $order = $this->order;
        $payments = collect(); // TenantPayment and TenantElectricityPayment collection

        TenantPayment::with('payLogs')
            ->where('tenant_contract_id', $tenantContract->id)
            ->where('is_charge_off_done', false)
            ->get()
            ->each(function ($tp) use ($payments) { $payments->push($tp); });
        TenantElectricityPayment::with('payLogs')
            ->where('tenant_contract_id', $tenantContract->id)
            ->where('is_charge_off_done', false)
            ->get()
            ->each(function ($tep) use ($payments) { $payments->push($tep); });

        $payments = $payments->sortBy(function ($tp) use ($order) {
            return $tp->due_time . '#' . ($order->search($tp->subject) + 10);
        })->values();

        foreach ($payments as $payment) {
            if ($amount === 0) {
                break;
            }

            $payLogData = [
                'subject'            => $payment->subject,
                'payment_type'       => $payment->subject === '電費' ? '電費' : '租金雜費',
                'virtual_account'    => $virtualAccount,
                'paid_at'            => $paidAt,
                'tenant_contract_id' => $tenantContract->id,
                'receipt_type'       => '發票'
            ];

            // previously paid total amount for this tenant payment(which is not enough)
            // it will be 0 if the payment wasn't paid before
            $alreadyPaid = $payment->payLogs->sum('amount');

            // by default(the tenant payment wasn't paid before),
            // the amount missing(or should be paid) is the amount of this tenant payment
            $shouldPayAmount = $payment->amount - $alreadyPaid;

            // if current amount is sufficient for the next payment
            if ( $amount - $shouldPayAmount >= 0 ) {
                // pay for this payment
                $amount = $amount - $shouldPayAmount;

                // mark tenant payment as deon
                $payment->update([
                    'is_charge_off_done' => true,
                    'charge_off_date'    => $paidAt,
                ]);

                // Set payLogData
                $payLogData['amount'] = $shouldPayAmount;
                if( $payment->collected_by == '房東'){
                    $payLogData['receipt_type'] = '收據';
                }
                $payLog = $payment->payLogs()->create($payLogData);
                // Error if reversal next period payment( set magic number temporarily )
                if ( $payment->due_time->diff($paidAt)->days > 28) {
                    $this->createReversalErrorCase('溢繳入帳', $payLog);
                }

                // previously paid total amount for this tenant payment(which is not enough)
                // it will be 0 if the payment wasn't paid before
                $alreadyPaid = $payment->payLogs->sum('amount');

                // by default(the tenant payment wasn't paid before),
                // the amount missing(or should be paid) is the amount of this tenant payment
                $shouldPayAmount = $payment->amount - $alreadyPaid;

                // determine who gets the income
                $paymentCollectedByCompany = $payment->subject != '電費' && $payment->collected_by === '公司';
                $electricityPaymentMethod = $tenantContract->room->building->electricity_payment_method;
                $electricityPaymentCollectedByCompany = $payment->subject == '電費' && $electricityPaymentMethod != '自行帳單繳付';
                $rentPayment = $payment->subject == '租金';
                if ($paymentCollectedByCompany || $electricityPaymentCollectedByCompany || $rentPayment) {
                    // generate company income
                    $incomeData = [
                        'subject'     => $payLogData['subject'],
                        'income_date' => $payLogData['paid_at'],
                        'amount'      => $payment->amount,
                    ];

                    if ($payment->subject === '租金') {
                        if ($tenantContract->room->management_fee_mode == '比例') {
                            $incomeData['amount'] = intval(round($shouldPayAmount * $tenantContract->room->management_fee / 100));
                        } else {
                            $incomeData['amount'] = intval(round($tenantContract->room->management_fee * ($shouldPayAmount / $payment->amount)));
                        }
                    }

                    $tenantContract->companyIncomes()->create($incomeData);
                }

            } else {
                // the remaining amount is insufficient for next payment
                // we will still generate a pay log for it
                $payLogData['amount'] = $amount;
                $payLog = $payment->payLogs()->create($payLogData);
                // Error if reversal next period payment( set magic number temporarily )
                if ($payment->due_time->diff($paidAt)->days > 28) {
                    $this->createReversalErrorCase('溢繳入帳', $payLog);
                }
                if ($payment->subject == '租金'){
                    $incomeData = [
                        'subject' => $payLogData['subject'],
                        'income_date' => $payLogData['paid_at'],
                        'amount' => 0
                    ];
                    if ($tenantContract->room->management_fee_mode == '比例') {
                        $income = intval(round($amount * $tenantContract->room->management_fee / 100));
                        $incomeData['amount'] = $income;
                    } else {
                        $incomeData['amount'] = intval(round($tenantContract->room->management_fee * ($amount / $payment->amount)));
                    }

                    $tenantContract->companyIncomes()->create($incomeData);
                }

                $amount = 0;
            }
        }

        return $amount; // 回傳未沖銷金額
    }

    function createReversalErrorCase(string $name, PayLog $payLog) {
        ReversalErrorCase::create(['name' => $name,  'date' => Carbon::now(), 'pay_log_id' => $payLog->id,]);
    }

    private function recordSumPaid(TenantContract $tenantContract, int $amount) {
        $tenantContract->sum_paid += $amount;
        $tenantContract->saveOrFail();
    }
}
