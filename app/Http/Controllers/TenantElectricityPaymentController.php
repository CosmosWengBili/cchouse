<?php

namespace App\Http\Controllers;

use App\Responser\FormDataResponser;
use App\TenantElectricityPayment;
use Illuminate\Http\Request;

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
}
