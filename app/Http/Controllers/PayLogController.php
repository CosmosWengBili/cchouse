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
        $unchargedPayments = TenantPayment::where([
            'tenant_contract_id' => $tenantContractId,
            'is_charge_off_done' => false,
        ])
            ->orderBy('due_time', 'asc')
            ->get();
            
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
            'pay_logs' => 'required|array',
            'pay_logs.*.loggable_type' => 'required',
            'pay_logs.*.loggable_id' => 'required|exists:tenant_payments,id',
            'pay_logs.*.subject' => 'required',
            'pay_logs.*.payment_type' => 'required',
            'pay_logs.*.amount' => 'required',
            'pay_logs.*.virtual_account' => 'required',
        ]);
        $commonAttrs = [
            'tenant_contract_id' => $validatedData['tenant_contract_id'],
            'come_from_bank' => $validatedData['come_from_bank'],
            'pay_sum' => $validatedData['pay_sum'],
            'paid_at' => $now,
        ];
        $payLogsAttrs = array_map(function ($payLogsAttr) use ($commonAttrs) {
            return array_merge($payLogsAttr, $commonAttrs);
        }, $validatedData['pay_logs']);

        DB::transaction(function () use ($now, $payLogsAttrs) {
            foreach ($payLogsAttrs as $payLogsAttr) {
                $payLog = PayLog::create($payLogsAttr);
                $payment = $payLog->loggable;
                $sum = $payment->payLogs()->sum('amount');
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
