<?php

namespace App\Http\Controllers;

use App\Responser\FormDataResponser;
use App\Responser\NestedRelationResponser;
use App\TenantElectricityPayment;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TenantElectricityPaymentController extends Controller
{
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
            "invoice_serial_number" => "required",
            "is_charge_off_done" => "required",
            "comment" => "required",
        ]);
        $tenantPayment = TenantElectricityPayment::create($validatedData);

        return redirect()->route('tenantPayments.index');
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
            "invoice_serial_number" => "required",
            "is_charge_off_done" => "required",
            "comment" => "required",
        ]);
        $tenantElectricityPayment->update($validatedData);

        return redirect()->route('tenantPayments.index');
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
