<?php

namespace App\Http\Controllers;

use App\Responser\FormDataResponser;
use App\Responser\NestedRelationResponser;
use App\TenantContract;
use App\TenantElectricityPayment;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TenantElectricityPaymentController extends Controller
{
    public function index(Request $request) {
        $responseData = new NestedRelationResponser();

        $tenantContracts = TenantContract::where('contract_end', '>', Carbon::now())
            ->where('electricity_payment_method', '公司代付')
            ->with($request->withNested)
            ->get();

        $data = $responseData
            ->index('TenantContracts', $tenantContracts)
            ->relations($request->withNested)
            ->get();

        return view('tenant_electricity_payments.index', $data);
    }

    public function create() {
        $responser = new FormDataResponser();
        $data = $responser->create(TenantElectricityPayment::class, 'tenantElectricityPayments.store')->get();

        return view('tenant_electricity_payments.form', $data);
    }

    public function store(Request $request) {
        $validatedData = $request->validate([
            "tenant_contract_id" => "required",
            "ammeter_read_date" => "required",
            "110v_start_degree" => "required",
            "110v_end_degree" => "required",
            "220v_start_degree" => "required",
            "220v_end_degree" => "required",
            "amount" => "required",
            "due_time" => "required",
            "invoice_serial_number" => "required",
            "is_charge_off_done" => "required",
            "comment" => "required",
        ]);
        $tenantPayment = TenantElectricityPayment::create($validatedData);

        return redirect($request->_redirect);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param TenantElectricityPayment $tenantElectricityPayment
     * @return \Illuminate\Http\Response
     */
    public function edit(TenantElectricityPayment $tenantElectricityPayment)
    {
        $responseData = new FormDataResponser();
        $data = $responseData->edit($tenantElectricityPayment, 'tenant_electricity_payments.update')->get();

        return view('tenant_electricity_payments.form', $data);
    }

    public function show(TenantElectricityPayment $tenantElectricityPayment)
    {
        $responseData = new NestedRelationResponser();
        $responseData
            ->show($tenantElectricityPayment->load($request->withNested))
            ->relations($request->withNested);

        return view('tenant_electricity_payments.show', $responseData->get());
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param TenantElectricityPayment $tenantElectricityPayment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, TenantElectricityPayment $tenantElectricityPayment)
    {
        $validatedData = $request->validate([
            "tenant_contract_id" => "required",
            "ammeter_read_date" => "required",
            "110v_start_degree" => "required",
            "110v_end_degree" => "required",
            "220v_start_degree" => "required",
            "220v_end_degree" => "required",
            "amount" => "required",
            "due_time" => "required",
            "invoice_serial_number" => "required",
            "is_charge_off_done" => "required",
            "comment" => "required",
        ]);
        $tenantElectricityPayment->update($validatedData);

        return redirect($request->_redirect);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param TenantElectricityPayment $tenantElectricityPayment
     * @return Response
     * @throws Exception
     */
    public function destroy(TenantElectricityPayment $tenantElectricityPayment)
    {
        $tenantElectricityPayment->delete();
        return response()->json(true);
    }
}
