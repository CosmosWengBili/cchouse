<?php

namespace App\Http\Controllers;

use App\Deposit;
use App\Exports\DebtCollectionExport;
use App\Exports\PayLogReportExport;
use App\PayLog;
use App\Services\InvoiceService;
use App\Responser\FormDataResponser;
use App\Responser\NestedRelationResponser;
use App\TenantContract;
use App\TenantElectricityPayment;
use App\TenantPayment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use function foo\func;

class PayLogController extends Controller
{
    public function index(Request $request) {
        $by = $request->input('by');
        $isExport = $request->input('submit_type') == 'export';

        if ($by == 'date') {
            if ($isExport) {
                return $this->exportByDate($request);
            }

            return $this->indexByDate($request);
        }
        return $this->indexByContract($request);
    }

    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @param PayLog $payLog
     * @return Response
     */
    public function show(Request $request, PayLog $payLog)
    {
        $responseData = new NestedRelationResponser();
        $responseData
            ->show($payLog->load($request->withNested))
            ->relations($request->withNested);

        return view('pay_logs.show', $responseData->get());
    }

    public function create(Request $request)
    {
        $tenantContractId = $request->input('tenantContractId');
        $tenantPayments = TenantPayment::where([
                'tenant_contract_id' => $tenantContractId,
                'is_charge_off_done' => false,
            ])
                ->orderBy('due_time', 'asc')
                ->get();
        $tenantElectricityPayments = TenantElectricityPayment::where([
            'tenant_contract_id' => $tenantContractId,
            'is_charge_off_done' => false,
        ])
            ->orderBy('due_time', 'asc')
            ->get();


        $unchargedPayments = $tenantPayments->concat($tenantElectricityPayments)
                                            ->sortBy('due_time');

            
        return view('pay_logs.massive_create_form', [
            'tenantContractId' => $tenantContractId,
            'unchargedPayments' => $unchargedPayments,
        ]);
    }

    public function edit(PayLog $payLog) {
        $responser = new FormDataResponser();
        $data = $responser->edit($payLog, 'payLogs.update')->get();

        return view('pay_logs.form', $data);
    }

    public function store(Request $request)
    {
        $now = Carbon::now();
        $validatedData = $request->validate([
            'tenant_contract_id' => 'required|exists:tenant_contract,id',
            'come_from_bank' => 'required',
            'pay_sum' => 'required',
            'paid_at' => 'required',
            'deposit_at' => 'required',
            'pay_logs' => 'required|array',
            'pay_logs.*.amount' => 'required',
            'pay_logs.*.comment' => 'nullable'
        ]);
        $commonAttrs = [
            'tenant_contract_id' => $validatedData['tenant_contract_id'],
            'come_from_bank' => $validatedData['come_from_bank'],
            'pay_sum' => $validatedData['pay_sum'],
            'paid_at' => Carbon::create($validatedData['paid_at']),
            'deposit_at' => Carbon::create($validatedData['deposit_at']),
            'virtual_account' => ' '
        ];
        $payLogsAttrs = array_map(function ($payLogsAttr) use ($commonAttrs) {
            return array_merge($payLogsAttr, $commonAttrs);
        }, $validatedData['pay_logs']);

        DB::transaction(function () use ($now, $payLogsAttrs, $validatedData) {
            foreach ($payLogsAttrs as $payLogsAttr) {
                $payLog = PayLog::create($payLogsAttr);
                $payment = $payLog->loggable;
                $sum = $payment->payLogs()->sum('amount');
                if(get_class($payment) == 'App\TenantElectricityPayment'){
                    $payment_type = '電費';
                }
                else{
                    $payment_type = '租金雜費';
                }
                $payLog->update(['subject' => $payment->subject, 'payment_type' => $payment_type ]);

                // from ReverseTenantPayments
                $tenantContract = TenantContract::find($validatedData['tenant_contract_id']);
                $paymentCollectedByCompany = $payment->subject != '電費' && $payment->collected_by == '公司';
                $electricityPaymentMethod = $tenantContract->room->building->electricity_payment_method;
                $electricityPaymentCollectedByCompany = $payment->subject == '電費' && in_array($electricityPaymentMethod, ['公司代付', '房東自行繳納']) && $building->activeContracts()['commission_type'] == '包租';
                $rentPayment = $payment->subject == '租金';
                if ($paymentCollectedByCompany || $electricityPaymentCollectedByCompany || $rentPayment) {
                    // generate company income
                    $incomeData = [
                        'subject'     => $payLog->subject,
                        'income_date' => $payLog->paid_at,
                        'amount'      => $payment->amount,
                    ];

                    if ($payment->subject == '租金') {
                        $incomeData['subject'] = '租金服務費';

                        if ($tenantContract->room->management_fee_mode == '比例') {
                            $incomeData['amount'] = intval(round($payLog->amount * $tenantContract->room->management_fee / 100));
                        } else {
                            $incomeData['amount'] = intval(round($tenantContract->room->management_fee * ($payLog->amount / $payment->amount)));
                        }

                        if($tenantContract->room->building->activeContracts()['commission_type'] == "包租"){
                            continue;
                        }
                    }

                    $tenantContract->companyIncomes()->create($incomeData);
                }


                if ($payment->amount == $sum) {
                    $payment->update(['is_charge_off_done' => true, 'charge_off_date' => $now]);
                }
            };
        });

        return redirect($request->_redirect);
    }

    public function update(Request $request, PayLog $payLog) {
        $validatedData = $request->validate([
            'loggable_type' => 'required',
            'loggable_id' => 'required',
            'subject' => 'required',
            'payment_type' => 'required',
            'amount' => 'required',
            'virtual_account' => 'required'
        ]);

        $result = InvoiceService::compareReceipt($payLog, $validatedData);
        if(!$result){
            $payLog->update($validatedData);
        }

        return redirect()->route('tenantPayments.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param PayLog $payLog
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(PayLog $payLog)
    {
        $payLog->delete();
        return response()->json(true);
    }

    public function transformToDeposit(Request $request, PayLog $payLog) {
        $validatedData = $request->validate([
            'deposit_collection_serial_number' => 'required',
            'payer_name' => 'required',
            'payer_certification_number' => 'required',
            'payer_is_legal_person' => 'required',
            'payer_phone' => 'required',
            'appointment_date' => 'required',
            'receiver' => 'required|exists:users,id',
        ]);

        $depositAttrs = array_merge($validatedData, [
            'tenant_contract_id' => $payLog->tenant_contract_id,
            'deposit_collection_date' => $payLog->paid_at,
            'invoicing_amount' => $payLog->amount,
            'reason_of_deletions' => '',
            'room_id' => $payLog->getRoomId(),
        ]);

        DB::transaction(function () use ($depositAttrs, $payLog) {
           Deposit::create($depositAttrs);
            $payLog->delete();
        });

        return back();
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    private function indexByContract(Request $request)
    {
        $responseData = new NestedRelationResponser();
        $selectColumns = array_merge(['tenant_contract.*'], TenantContract::extraInfoColumns());
        $selectStr = DB::raw(join(', ', $selectColumns));
        $responseData
            ->index(
                'tenant_contracts',
                $this->limitRecords(
                    TenantContract::withExtraInfo()
                        ->select($selectStr)
                        ->with($request->withNested)
                        ->active()
                )
            )
            ->relations($request->withNested);

        return view('pay_logs.index_by_contract', $responseData->get());
    }

    private function indexByDate(Request $request) {
        $startDateStr = $request->input('start_date');
        $endDateStr = $request->input('end_date');
        $data = $this->generateDataByDate($startDateStr, $endDateStr);

        return view('pay_logs.index_by_date', $data);
    }

    private function exportByDate(Request $request) {
        $startDateStr = $request->input('start_date');
        $endDateStr = $request->input('end_date');
        $data = $this->generateDataByDate($startDateStr, $endDateStr);

        return Excel::download(
            new PayLogReportExport($data['tableRows']),
            "${startDateStr}~${endDateStr}-現金流報表.xlsx"
        );
    }

    public function changeLoggable(Request $request, PayLog $payLog) {
        $model = $request->input('model');
        $subject = $request->input('subject');
        $payLog->update([
            'loggable_type'=> get_class(app("App\\". $model)),
            'loggable_id' => 0,
            'subject' => $subject
        ]);

        return response()->json(true);
    }

    /**
     * @param $startDateStr
     * @param $endDateStr
     * @return array
     */
    private function generateDataByDate($startDateStr, $endDateStr): array
    {
        $data = ['tableRows' => [], 'total' => 0];
        $rows = [];
        $payLogs = PayLog::with(['tenantContract.room.building', 'loggable'])
            ->whereBetween('paid_at', [$startDateStr, $endDateStr])
            ->where('loggable_type','<>', 'App\OverPayment')
            ->get()
            ->sortByDesc('paid_at');
        foreach ($payLogs as $payLog) {
            $rows[] = [
                '繳費科目' => $payLog->subject,
                '繳費費用' => $payLog->amount,
                '繳費虛擬帳號' => $payLog->virtual_account,
                '繳費日期' => $payLog->paid_at,
                '應繳時間' => $payLog->loggable['due_time'] == null ? '' : $payLog->loggable['due_time']->format('Y-m-d'),
                '承租方式' => $payLog->getCommissionType(),
                '應繳費用' => $payLog->loggable['amount'],
                '匯款總額' => $payLog->pay_sum,
            ];
        }


        $total = $payLogs->sum('amount');
        $data['tableRows'] = $rows;
        $data['total'] = $total;

        return $data;
    }
}
