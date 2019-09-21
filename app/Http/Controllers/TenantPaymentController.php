<?php

namespace App\Http\Controllers;

use App\Responser\FormDataResponser;
use App\Responser\NestedRelationResponser;
use App\Services\TenantPaymentService;
use App\Services\ReceiptService;
use App\TenantContract;
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
            'tenant_contract_id' => 'required|exists:tenant_contract,id',
            'subject' => 'required',
            'due_time' => 'required',
            'amount' => 'required',
            'is_charge_off_done' => 'required',
            'charge_off_date' => 'required',
            'collected_by' => 'required',
            'is_visible_at_report' => 'required',
            'is_pay_off' => 'required',
            'comment' => 'required',
        ]);
        $tenantPayment = TenantPayment::create($validatedData);

        return redirect($request->_redirect);
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
            'tenant_contract_id' => 'required|exists:tenant_contract,id',
            'subject' => 'required',
            'due_time' => 'required',
            'amount' => 'required',
            'is_charge_off_done' => 'required',
            'charge_off_date' => 'required',
            'collected_by' => 'required',
            'is_visible_at_report' => 'required',
            'is_pay_off' => 'required',
            'comment' => 'required',
        ]);
        ReceiptService::compareReceipt($tenantPayment, $validatedData);
        $tenantPayment->update($validatedData);

        return redirect($request->_redirect);
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

    private function buildTableRows($startDate, $endDate) {
        return TenantPaymentService::buildTenantPaymentTableRows($startDate, $endDate);
    }
}
