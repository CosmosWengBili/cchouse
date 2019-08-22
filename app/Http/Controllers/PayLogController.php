<?php

namespace App\Http\Controllers;

use App\PayLog;
use App\Responser\FormDataResponser;
use Illuminate\Http\Request;

class PayLogController extends Controller
{
    public function create() {
        $responser = new FormDataResponser();
        $data = $responser->create(PayLog::class, 'payLogs.store')->get();

        return view('pay_logs.form', $data);
    }

    public function store(Request $request) {
        $validatedData = $request->validate([
            'loggable_type' => 'required',
            'loggable_id' => 'required',
            'subject' => 'required',
            'payment_type' => 'required',
            'amount' => 'required',
            'virtual_account' => 'required',
            'paid_at' => 'required',
        ]);
        $payment = $validatedData['loggable_type']::find($validatedData['loggable_id']);
        $tenantContractId = $payment->tenant_contract_id;
        $payLog = PayLog::create(array_merge($validatedData, ['tenant_contract_id' => $tenantContractId]));

        return redirect()->route('tenantPayments.index');
    }
}
