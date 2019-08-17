<?php

namespace App\Http\Controllers;

use App\Responser\FormDataResponser;
use App\Responser\NestedRelationResponser;
use App\TenantContract;
use App\TenantElectricityPayment;
use App\TenantPayment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TenantPaymentController extends Controller
{
    public function index(Request $request) {
        $by = $request->input('by');

        if ($by == 'date') {
            return $this->indexByDate($request);
        }
        return $this->indexByContract($request);
    }

    public function show(Request $request, TenantPayment $tenantPayment)
    {
        $responseData = new NestedRelationResponser();
        $responseData
            ->show($tenantPayment->load($request->withNested))
            ->relations($request->withNested);

        return view('tenant_payments.show', $responseData->get());
    }


    public function create() {
        $responser = new FormDataResponser();
        $data = $responser->create(TenantPayment::class, 'tenantPayments.store')->get();

        return view('tenant_payments.form', $data);
    }

    public function store(Request $request) {
        $validatedData = $request->validate([
            'tenant_contract_id' => 'required',
            'due_time' => 'required',
            'amount' => 'required',
            'is_charge_off_done' => 'required',
            'charge_off_date' => 'required',
            'invoice_serial_number' => 'required',
            'collected_by' => 'required',
            'is_visible_at_report' => 'required',
            'is_pay_off' => 'required',
            'comment' => 'required',
        ]);
        $tenantPayment = TenantPayment::create($validatedData);

        return redirect()->route('tenantPayments.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param TenantPayment $tenantPayment
     * @return \Illuminate\Http\Response
     */
    public function edit(TenantPayment $tenantPayment)
    {
        $responseData = new FormDataResponser();
        $data = $responseData->edit($tenantPayment, 'tenantPayments.update')->get();

        return view('tenant_payments.form', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param TenantPayment $tenantPayment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, TenantPayment $tenantPayment)
    {
        $validatedData = $request->validate([
            'tenant_contract_id' => 'required',
            'due_time' => 'required',
            'amount' => 'required',
            'is_charge_off_done' => 'required',
            'charge_off_date' => 'required',
            'invoice_serial_number' => 'required',
            'collected_by' => 'required',
            'is_visible_at_report' => 'required',
            'is_pay_off' => 'required',
            'comment' => 'required',
        ]);
        $tenantPayment->update($validatedData);

        return redirect()->route('tenantPayments.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param TenantPayment $tenantPayment
     * @return Response
     */
    public function destroy(TenantPayment $tenantPayment)
    {
        $tenantPayment->delete();
        return response()->json(true);
    }

    private function indexByDate(Request $request) {
        $data = [];
        $startDateString = $request->input('start_date');
        $endDateString = $request->input('end_date');

        if ($startDateString && $endDateString) {
            $startDate = Carbon::parse($startDateString);
            $endDate = Carbon::parse($endDateString);

            $data['tableRows'] = $this->buildTableRows($startDate, $endDate);
        }

        return view('tenant_payments.index_by_date', $data);
    }

    private function indexByContract(Request $request) {
        $responseData = new NestedRelationResponser();
        $tenantContracts = TenantContract::where('contract_end', '>', Carbon::now())
            ->with($request->withNested)
            ->get();
        $data = $responseData
                    ->index('TenantContracts', $tenantContracts)
                    ->relations($request->withNested)
                    ->get();

        return view('tenant_payments.index_by_contract', $data);
    }

    private function buildTableRows(Carbon $startDate, Carbon $endDate) {
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
