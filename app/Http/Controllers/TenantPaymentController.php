<?php

namespace App\Http\Controllers;

use App\Classes\NotifyUsers;
use App\Classes\TextContent;
use App\Responser\FormDataResponser;
use App\Responser\NestedRelationResponser;
use App\Services\TenantPaymentService;
use App\Services\InvoiceService;
use App\TenantContract;
use App\TenantPayment;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

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
            'charge_off_date' => '',
            'collected_by' => 'required',
            'is_visible_at_report' => 'required',
            'comment' => '',
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
            'charge_off_date' => '',
            'collected_by' => 'required',
            'is_visible_at_report' => 'required',
            'comment' => '',
        ]);

        if ($tenantPayment->is_charge_off_done) {
            $this->generateEditorialReview($tenantPayment, $validatedData);
            //Notify specific manager
            $user = User::find(1);
            $notify = new NotifyUsers($user);
            $content = new TextContent($this->makeTenantPaymentUpdatedContent('updated', $tenantPayment));
            $notify->notifySelf($content);
        } else {
            $result = InvoiceService::compareReceipt($tenantPayment, $validatedData);
            if(!$result){
                $tenantPayment->update($validatedData);
            }
        }

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
        if ($tenantPayment->is_charge_off_done) {
            return response()->json(['errors' => ['已沖銷科目不得刪除']], 422);
        }
        $tenantPayment->delete();
        return response()->json(true);
    }

    private function indexByDate(Request $request) {
        $data = [];
        $roomCode = $request->input('room_code');
        $tenantName = $request->input('tenant_name');
        $startDateString = $request->input('start_date');
        $endDateString = $request->input('end_date');

        if ($roomCode || $tenantName || $startDateString || $endDateString) {
            $startDate = Carbon::parse($startDateString);
            $endDate = Carbon::parse($endDateString);

            $data['tableRows'] = $this->buildTableRows($roomCode, $tenantName, $startDate, $endDate);
        }

        return view('tenant_payments.index_by_date', $data);
    }

    private function indexByContract(Request $request) {
        $responseData = new NestedRelationResponser();
        $selectColumns = array_merge(['tenant_contract.*'], TenantContract::extraInfoColumns());
        $selectStr = DB::raw(join(', ', $selectColumns));
        $tenantContracts = TenantContract::withExtraInfo()
            ->where('contract_end', '>', Carbon::now())
            ->with($request->withNested)
            ->select($selectStr)
            ->get();
        $data = $responseData
                    ->index('TenantContracts', $tenantContracts)
                    ->relations($request->withNested)
                    ->get();

        return view('tenant_payments.index_by_contract', $data);
    }

    private function buildTableRows(?string $roomCode, ?string $tenantName, Carbon $startDate, Carbon$endDate) {
        return TenantPaymentService::buildTenantPaymentTableRows($roomCode, $tenantName, $startDate, $endDate);
    }

    private function makeTenantPaymentUpdatedContent(string $type, TenantPayment $tenantPayment)
    {
        $now = Carbon::now();
        $id = $tenantPayment->id;
        $comment = $tenantPayment->comment;

        switch ($type) {
            case 'deleted':
                $content = "應繳用費編號: {$id} 資料被申請刪除，備註: {$comment}。";
                break;
            default:
                $content = "應繳用費編號: {$id} 資料被申請更新，請立即前往確認，備註: {$comment}。";
        }

        return $content;
    }
}
