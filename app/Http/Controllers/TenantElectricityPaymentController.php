<?php

namespace App\Http\Controllers;

use App\Exports\TenantElectricityPaymentExport;
use App\Imports\TenantElectricityPaymentImport;
use App\PayLog;
use App\Responser\FormDataResponser;
use App\Responser\NestedRelationResponser;
use App\Services\InvoiceService;
use App\TenantContract;
use App\TenantElectricityPayment;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class TenantElectricityPaymentController extends Controller
{
    public function index(Request $request) {
        $type = $request->input('type');

        if ($type == 'charged') { // 儲值電
            return $this->renderChargedIndex($request);
        } else {
            return $this->renderIndex($request);
        }
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
            "is_charge_off_done" => "required",
            'charge_off_date' => '',
            'comment' => '',
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
        $data = $responseData->edit($tenantElectricityPayment, 'tenantElectricityPayments.update')->get();

        return view('tenant_electricity_payments.form', $data);
    }

    public function show(Request $request, TenantElectricityPayment $tenantElectricityPayment)
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
            "is_charge_off_done" => "required",
            'charge_off_date' => '',
            'comment' => '',
        ]);

        $result = InvoiceService::compareReceipt($tenantElectricityPayment, $validatedData);
        if(!$result){
            $tenantElectricityPayment->update($validatedData);
        }

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
        if ($tenantElectricityPayment->is_charge_off_done) {
            return response()->json(['errors' => ['已沖銷科目不得刪除']], 422);
        }
        $tenantElectricityPayment->delete();
        return response()->json(true);
    }

    public function sendReportSMSToAll(Request $request) {
        $year = intval($request->input('year'));
        $month = intval($request->input('month'));

        $this->findRelatedTenantContracts()
             ->chunk(100, function($tenantContracts) use ($year, $month) {
                 foreach($tenantContracts as $tenantContract)
                 {
                     $tenantContract->sendElectricityPaymentReportSMS($year, $month);
                 }
             });

        return response()->json(true);
    }

    public function downloadImportFile() {
        return Excel::download(new TenantElectricityPaymentExport(), '電費批次匯入表.xlsx');
    }

    public function importFile(Request $request) {
        try {
            Excel::import(
                new TenantElectricityPaymentImport(),
                $request->file('excel')
            );
            return redirect()->back();
        } catch (\Throwable $th) {
            return redirect()->back()->with('alert', $th->getMessage());
        }
    }

    private function findRelatedTenantContracts($type = null, $startDate = null, $endDate = null, $roomCode = null)
    {
        $relation = TenantContract::withExtraInfo();

        if ($type == 'charged') {
            $relation = $relation->where('rooms.electricity_virtual_account', '!=', '');
        }

        return $relation->where('contract_end', '>', Carbon::now())
                        ->where('buildings.electricity_payment_method', '公司代付');
    }

    private function renderIndex(Request $request)
    {
        $responseData = new NestedRelationResponser();
        $selectColumns = array_merge(['tenant_contract.*'], TenantContract::extraInfoColumns());
        $selectStr = DB::raw(join(', ', $selectColumns));
        $tenantContracts = $this->limitRecords(
            $this->findRelatedTenantContracts()
                ->select($selectStr)
                ->with($request->withNested)
        );
        $data = $responseData
            ->index('TenantContracts', $tenantContracts)
            ->relations($request->withNested)
            ->get();

        return view('tenant_electricity_payments.index', $data);
    }

    private function renderChargedIndex(Request $request) {
        $roomCode = $request->input('room_code');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $relation = PayLog::join('tenant_contract', 'tenant_contract.id', '=', "pay_logs.tenant_contract_id")
                          ->join('rooms', 'rooms.id', '=', "tenant_contract.room_id")
                          ->where('rooms.electricity_virtual_account', '!=', '')
                          ->where('loggable_type', 'App\Room');

        if ($roomCode) {
            $relation = $relation->where('rooms.room_code', $roomCode);
        }
        if ($startDate) {
            $startDate = Carbon::parse($startDate);
            $relation->where('pay_logs.paid_at', '>=', $startDate);
        }
        if ($endDate) {
            $endDate = Carbon::parse($endDate);
            $relation->where('pay_logs.paid_at', '<=', $endDate);
        }

        $relation = $relation->select('pay_logs.*')->groupBy('pay_logs.id');
        $responseData = new NestedRelationResponser();
        $data = $responseData->index('PayLogs', $relation->get())->get();

        return view('tenant_electricity_payments.charged_index', $data);
    }
}
