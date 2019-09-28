<?php

namespace App\Listeners;

use App\Events\ReceivableArrived;
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
use App\Notifications\AbnormalPaymentReceived;
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
            $restAmount = $this->reverse($tenantContract, $virtualAccount, $paidAt, $amount);
            $this->recordSumPaid($tenantContract, $amount); // 紀錄「已繳總額」

            if($restAmount > 0) { //
                $payLogData = [
                    'subject'            => '溢繳無法沖銷費用',
                    'payment_type'       => '租金雜費',
                    'virtual_account'    => $virtualAccount,
                    'paid_at'            => $paidAt,
                    'amount'             => $amount,
                    'tenant_contract_id' => $tenantContract->id,
                    'loggable_type'      => 'OverPayment',
                    'loggable_id'        => 0, // 0 為溢繳費用（不關連至任何 TenantPayment 或 TenantElectricityPayment)
                ];
                PayLog::create($payLogData);
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

                // generate a pay log
                $payLogData['amount'] = $shouldPayAmount;
                $payment->payLogs()->create($payLogData);

                // determine who gets the income
                $paymentCollectedByCompany = $payment->collected_by === '公司';
                $electricityPaymentCollectedByCompany = $payment->subject == '電費' &&
                    $tenantContract->electricity_payment_method != '自行帳單繳付';
                if ($paymentCollectedByCompany || $electricityPaymentCollectedByCompany) {
                    // generate company income
                    $incomeData = [
                        'subject'     => $payLogData['subject'],
                        'income_date' => $payLogData['paid_at'],
                        'amount'      => $payment->amount,
                    ];

                    if ($payment->subject === '租金') {
                        if ($tenantContract->room->management_fee_mode === '比例') {
                            $income = intval(round($payment->amount * $tenantContract->room->management_fee / 100));
                            $incomeData['amount'] = $income;
                        } else {
                            $incomeData['amount'] = intval($tenantContract->room->management_fee);
                        }
                    }

                    $tenantContract->companyIncomes()->create($incomeData);

                } else if ($payment->collected_by === '房東') {
                    // generate landlord other subject
                    LandlordOtherSubject::create([
                        'subject'           => $payLogData['subject'],
                        'subject_type'      => $payLogData['payment_type'],
                        'income_or_expense' => '收入',
                        'expense_date'      => $payLogData['paid_at'],
                        'amount'            => $payment->amount,
                        'room_id'           => $tenantContract->room->id,
                    ]);
                }


            } else {
                // the remaining amount is insufficient for next payment
                // we will still generate a pay log for it
                $payLogData['amount'] = $amount;
                $payment->payLogs()->create($payLogData);
                $amount = 0;
            }

            // if transaction date is not within next period of payment
            // it's considered as abnormal, and a notification is needed
            $period = $payment->period ?? '月';
            $txTime = $this->periodService->next($paidAt, $period)->startOfDay();
            $dueTime = Carbon::parse($payment->due_time);
            if ($txTime->lte($dueTime)) {
                $tenantContract->commissioner->notify(new AbnormalPaymentReceived($payment));
            }
        }

        return $amount; // 回傳未沖銷金額
    }

    private function recordSumPaid(TenantContract $tenantContract, int $amount) {
        $tenantContract->sum_paid += $amount;
        $tenantContract->saveOrFail();
    }
}
