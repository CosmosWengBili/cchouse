<?php

namespace App\Http\Controllers;

use App\LandlordOtherSubject;
use App\PayLog;
use App\PayOff;
use App\Responser\FormDataResponser;
use App\Responser\NestedRelationResponser;
use App\Services\PayOffService;
use App\Tenant;
use App\TenantContract;
use App\TenantElectricityPayment;
use App\TenantPayment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PayOffController extends Controller
{
    public function index(Request $request) {
        $responseData = new NestedRelationResponser();
        $selectColumns = array_merge(['tenant_contract.*'], TenantContract::extraInfoColumns());
        $selectStr = DB::raw(join(', ', $selectColumns));
        $responseData
            ->index(
                'tenant_contracts',
                $this->limitRecords(
                    TenantContract::withExtraInfo()
                        ->with($request->withNested)
                        ->active()
                        ->select($selectStr)
                )
            )
            ->relations($request->withNested);

        return view('pay_offs.index', $responseData->get());
    }

    public function show(Request $request, TenantContract $tenantContract) {
        $payOffDate = $request->input('payOffDate');
        $returnWay = $request->input('return_ways');

        if ($payOffDate) {
            $payOffDate = new Carbon($payOffDate);
            $payOffService = new PayOffService($payOffDate, $tenantContract, $returnWay);
            $payOffData = $payOffService->buildPayOffData();

            $headerInfo = $this->getHeaderInfo($tenantContract);
        }

        return view('pay_offs.show', [
            'tenantContract' => $tenantContract,
            'payOffDate' => $payOffDate,
            'payOffData' => $payOffData ?? null,
            'headerInfo' => $headerInfo ?? null,
        ]);
    }

    public function history(Request $request, TenantContract $tenantContract) {

        $payOff = PayOff::where('tenant_contract_id', $tenantContract->id)
                ->get()
                ->last();
        $responseData = new NestedRelationResponser();
        $responseData
            ->show($payOff->load($request->withNested))
            ->relations($request->withNested);


       return view('pay_offs.history', $responseData->get());
    }

    public function storePayOffPayments(Request $request, TenantContract $tenantContract)
    {
        // 電費相關
        $validatedElectricityData = $request->validate([
            'electricity.final_110v' => 'required|integer',
            'electricity.final_220v' => 'required|integer',
            'electricity.old_110v' => 'required|integer',
            'electricity.old_220v' => 'required|integer',
        ])['electricity'];
        // 表頭相關
        $validatedHeaderData = $request->validate([
            'header.pay_off_date' => 'required|date',
            'header.commission_type' => 'required',
            'header.return_ways' => 'required',
        ])['header'];
        // 科目相關
        $validatedItemsData = $request->validate([
            'items.*.subject' => 'required',
            'items.*.amount' => 'required|integer',
            'items.*.collected_by' => 'nullable',
            'items.*.comment' => 'nullable',
        ])['items'];
        // 總和相關
        $validatedSumsData = $request->validate([
            'sums.refund_amount' => 'required',
            'sums.should_received' => 'required',
            'sums.should_pay' => 'required',
        ])['sums'];

        try {
            $this->handlePayOffPayments(
                $tenantContract,
                $validatedElectricityData,
                $validatedHeaderData,
                $validatedItemsData,
                $validatedSumsData
            );
        } catch (\Exception $e) {
            return response()->json(false);
        }

        return response()->json(true);
    }

    /**
     * @param TenantContract $tenantContract
     * @param                $validatedElectricityData
     * @param                $validatedHeaderData
     * @param                $validatedItemsData
     * @param                $validatedSumsData
     */
    private function handlePayOffPayments(TenantContract $tenantContract, $validatedElectricityData, $validatedHeaderData, $validatedItemsData, $validatedSumsData)
    {
        DB::transaction(function () use ($tenantContract,
                                  $validatedElectricityData,
                                  $validatedHeaderData,
                                  $validatedItemsData,
                                  $validatedSumsData)
        {
            PayOff::create([
                'pay_off_type' => $validatedHeaderData['return_ways'],
                '110v_degree' => $validatedElectricityData['final_110v'],
                '220v_degree' => $validatedElectricityData['final_220v'],
                'payment_detail' => $validatedItemsData,
                'tenant_amount' => $validatedSumsData['refund_amount'],
                'company_income' => $validatedSumsData['should_received'],
                'landlord_paid' => $validatedSumsData['should_pay'],
                'tenant_contract_id' => $tenantContract->id,
            ]);

            $room_id = $tenantContract->room->id;
            // 產生點交盈餘相關科目
            $landlordOtherSubjects = collect($validatedItemsData)
                                    ->where('subject', '點交中退盈餘分配');
            if($landlordOtherSubjects->count() != 0){
                $landlordOtherSubject = $landlordOtherSubjects->first();
                LandlordOtherSubject::create([
                    'subject' => $landlordOtherSubject['subject'],
                    'subject_type' => '點交',
                    'income_or_expense' => '支出',
                    'expense_date' => now(),
                    'amount' => $landlordOtherSubject['amount'],
                    'comment' => $landlordOtherSubject['comment'],
                    'room_id' => $room_id,
                    'is_invoiced' => true,
                    'invoice_item_name' => '管理服務費'
                ]);
            }
            

            $payOffDate = $validatedHeaderData['pay_off_date'];
            // 產生 payments
            $tenantPayments = collect($validatedItemsData)
                ->where('subject', '<>', '電費')
                ->where('subject', '<>', '點交中退盈餘分配')
                ->where('amount', '<>', '0')
                ->map(function ($payment) use ($tenantContract, $payOffDate) {
                    $subject = $payment['subject'];
                    $amount = -($payment['amount']);
                    $comment = is_null($payment['comment']) ? '' : $payment['comment'];
                    $collected_by = is_null($payment['collected_by'])
                        ? '公司'
                        : $payment['collected_by'];
                    if( strpos($subject, '折抵') == false ){
                        return new TenantPayment([
                            'tenant_contract_id' => $tenantContract->id,
                            'due_time' => $payOffDate,
                            'subject' => $subject,
                            'amount' => $amount,
                            'is_charge_off_done' => true,
                            'charge_off_date' => $payOffDate,
                            'collected_by' => $collected_by,
                            'is_visible_at_report' =>false,
                            'is_pay_off' => true,
                            'comment' => $comment,
                            'period' => '次'
                        ]);
                    }
            });
            // 產生 electricity payments
            $tenantElectricityPayments = collect($validatedItemsData)
                ->where('subject', '電費')
                ->where('amount', '<>', '0')
                ->map(function ($payment) use ($tenantContract, $payOffDate, $validatedElectricityData) {
                    $subject = $payment['subject'];
                    $amount = abs($payment['amount']);
                    $comment = is_null($payment['comment']) ? '': $payment['comment'];

                    return new TenantElectricityPayment([
                        'tenant_contract_id' => $tenantContract->id,
                        'subject' => $subject,
                        'ammeter_read_date' => now(),
                        'due_time' => $payOffDate,
                        '110v_start_degree' => $validatedElectricityData['old_110v'],
                        '110v_end_degree' => $validatedElectricityData['final_110v'],
                        '220v_start_degree' => $validatedElectricityData['old_220v'],
                        '220v_end_degree' => $validatedElectricityData['final_220v'],
                        'amount' => $amount,
                        'is_charge_off_done' => true,
                        'comment' => $comment,
                        'charge_off_date' => $payOffDate,
                        'is_pay_off' => true
                    ]);
            });

            // save payments
            $tenantContract->tenantPayments()->saveMany($tenantPayments);
            // save electricity payment
            $tenantContract->tenantElectricityPayments()->saveMany($tenantElectricityPayments);

            $virtual_account = $tenantContract->room->virtual_account;

            // 新產生的點交科目is_old=false，如果為負數，也要能產生對應的 paylog，費用等同此科目費用，但轉化為正數 ;
            $allPayments = $tenantPayments->merge($tenantElectricityPayments);
            foreach ($allPayments as $payment) {
                if ( (int) $payment->amount != 0) {
                    $amount = (int) $payment->amount;
                    $subject = $payment->subject;

                    if ($payment instanceof TenantElectricityPayment) {
                        $tenantContract->payLogs()->create([
                            'loggable_type' => TenantElectricityPayment::class,
                            'loggable_id' => $payment->id,
                            'subject' => $subject,
                            'payment_type' => '電費',
                            'amount' => $amount,
                            'virtual_account' => $virtual_account,
                            'paid_at' => now(),
                        ]);
                    } else {
                        $tenantContract->payLogs()->create([
                            'loggable_type' => TenantPayment::class,
                            'loggable_id' => $payment->id,
                            'subject' => $subject,
                            'payment_type' => '租金雜費',
                            'amount' => $amount,
                            'virtual_account' => $virtual_account,
                            'paid_at' => now(),
                        ]);
                    }
                }
            }
        });
    }

    /**
     * 取得表頭資訊
     * @param TenantContract $tenantContract
     *
     * @return array
     */
    private function getHeaderInfo(TenantContract $tenantContract)
    {
        return [
            'commission_type' => $tenantContract->building->landlordContracts()->active()->commission_type,
            'room_id' => $tenantContract->room->id,
            'location' => $tenantContract->building->location,
            'tenant_name' => $tenantContract->tenant->name,
        ];
    }

}
