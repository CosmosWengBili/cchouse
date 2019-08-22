<?php

namespace App\Listeners;

use App\Events\ReceivableArrived;
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

class ReverseTenantPayments
{
    protected $periodService;
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(PeriodService $periodService)
    {
        $this->periodService = $periodService;
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
        
        $res = DB::transaction(function () use ($event) {

            $order = SystemVariable::ofGroup('Reversal')->pluck('code');
    
            $amount = intval($event->data['amount']);
            $virtualAccount = $event->data['virtual_account'];
            $paidAt = $event->data['txTime'];
    
            $tenantContract = $event->tenantContract;
            $tenantPayments = TenantPayment::with('payLogs')
                                            ->where('tenant_contract_id', $tenantContract->id)
                                            ->where('is_charge_off_done', false)
                                            ->get();
            
            $tenantPayments = $tenantPayments->sortBy(function ($tp) use ($order) {
                return $tp->due_time . '#' . ($order->search($tp->subject) + 10);
            })->values();
    
            
            foreach ($tenantPayments as $tp) {
    
                if ($amount === 0) {
                    break;
                }
                
                $payLogData = [
                    'subject'            => $tp->subject,
                    'payment_type'       => $tp->subject === '電費' ? '電費' : '租金雜費',
                    'virtual_account'    => $virtualAccount,
                    'paid_at'            => $paidAt,
                    'tenant_contract_id' => $tenantContract->id,
                ];
     
                // previously paid total amount for this tenant payment(which is not enough)
                // it will be 0 if the payment wasn't paid before
                $alreadyPaid = $tp->payLogs->sum('amount');
                
                // by default(the tenant payment wasn't paid before), 
                // the amount missing(or should be paid) is the amount of this tenant payment
                $shuldPayAmount = $tp->amount - $alreadyPaid;
    
    
                // if current amount is sufficient for the next payment
                if ( $amount - $shuldPayAmount >= 0 ) {
                    // pay for this payment
                    $amount = $amount - $shuldPayAmount;
    
                    // mark tenant payment as deon
                    $tp->update([
                        'is_charge_off_done' => true,
                        'charge_off_date'    => $paidAt,
                    ]);
    
                    // generate a pay log
                    $payLogData['amount'] = $shuldPayAmount;
                    $tp->payLogs()->create($payLogData);
    
                    // determine who gets the income
                    if ($tp->collected_by === '公司') {
                        // generate company income
                        $incomeData = [
                            'subject'     => $payLogData['subject'],
                            'income_date' => $payLogData['paid_at'],
                            'amount'      => $payLogData['amount'],
                        ];
    
                        if ($tp->subject === '租金') {
                            if ($tenantContract->room->management_fee_mode === '比例') {
                                $income = intval(round($tenantContract->room->rent_actual * $tenantContract->room->management_fee / 100));
                                $incomeData['amount'] = $income;
                            } else {
                                $incomeData['amount'] = intval($tenantContract->room->management_fee);
                            }
                        }
                        
                        $tenantContract->companyIncomes()->create($incomeData);
    
                    } else if ($tp->collected_by === '房東') {
                        // generate landlord other subject
                        LandlordOtherSubject::create([
                            'subject'           => $payLogData['subject'],
                            'subject_type'      => $payLogData['payment_type'],
                            'income_or_expense' => '收入',
                            'expense_date'      => $payLogData['paid_at'],
                            'amount'            => $payLogData['amount'],
                            'room_id'           => $tenantContract->room->id,
                        ]);
                    }
    
    
                } else {
                    // the remaining amount is insufficient for next payment
                    // we will still generate a pay log for it
                    $payLogData['amount'] = $amount;
                    $tp->payLogs()->create($payLogData);
                    $amount = 0;
                }
    
                // if transaction date is not within next period of payment
                // it's considered as abnormal, and a notification is needed
                if ($this->periodService->next($event->data['txTime'], $tp->period)->startOfDay()->lte(Carbon::parse($tp->due_time))) {
                    $tenantContract->commissioner->notify(new AbnormalPaymentReceived($tp));
                }
            }

            return true;
        });

        $result['success'] = $res;

        return $result;
    }
}
