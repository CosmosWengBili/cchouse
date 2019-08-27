<?php

namespace App\Http\Controllers;

use App\PayLog;
use App\Responser\FormDataResponser;
use App\Responser\NestedRelationResponser;
use App\TenantContract;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PayLogController extends Controller
{
    public function index(Request $request) {
        $responseData = new NestedRelationResponser();
        $responseData
            ->index(
                'tenant_contracts',
                TenantContract::with($request->withNested)->active()->get()
            )
            ->relations($request->withNested);

        return view('pay_logs.index', $responseData->get());
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

    public function create() {
        $responser = new FormDataResponser();
        $data = $responser->create(PayLog::class, 'payLogs.store')->get();

        return view('pay_logs.form', $data);
    }

    public function edit(PayLog $payLog) {
        $responser = new FormDataResponser();
        $data = $responser->edit($payLog, 'payLogs.update')->get();

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

        return redirect($request->_redirect);
    }

    public function update(Request $request, PayLog $payLog) {
        $validatedData = $request->validate([
            'loggable_type' => 'required',
            'loggable_id' => 'required',
            'subject' => 'required',
            'payment_type' => 'required',
            'amount' => 'required',
            'virtual_account' => 'required',
            'paid_at' => 'required',
        ]);
        $payLog->update($validatedData);

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
}
