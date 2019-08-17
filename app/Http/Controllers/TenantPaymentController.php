<?php

namespace App\Http\Controllers;

use App\Responser\FormDataResponser;
use App\Responser\NestedRelationResponser;
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
        $responseData = new NestedRelationResponser();
        $tenantContracts = TenantContract::where('contract_end', '>', Carbon::now())
            ->with($request->withNested)
            ->get();
        $data = $responseData
            ->index('TenantContracts', $tenantContracts)
            ->relations($request->withNested)
            ->get();

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
}
