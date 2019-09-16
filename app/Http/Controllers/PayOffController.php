<?php

namespace App\Http\Controllers;

use App\PayLog;
use App\Responser\FormDataResponser;
use App\Responser\NestedRelationResponser;
use App\Services\PayOffService;
use App\Tenant;
use App\TenantContract;
use App\TenantPayment;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PayOffController extends Controller
{
    public function index(Request $request) {
        $responseData = new NestedRelationResponser();
        $responseData
            ->index(
                'tenant_contracts',
                $this->limitRecords(TenantContract::with($request->withNested)->active())
            )
            ->relations($request->withNested);

        return view('pay_offs.index', $responseData->get());
    }

    public function show(Request $request, TenantContract $tenantContract) {
        $payOffDate = $request->input('payOffDate');

        if ($payOffDate) {
            $payOffDate = new Carbon($payOffDate);
            $payOffService = new PayOffService($payOffDate, $tenantContract);
            $payOffData = $payOffService->buildPayOffData();
        }

        return view('pay_offs.show', [
            'tenantContract' => $tenantContract,
            'payOffDate' => $payOffDate,
            'payOffData' => $payOffData ?? null,
        ]);
    }

    public function storePayOffPayments(Request $request, TenantContract $tenantContract) {
        $payments = $request->input('payments');
        $tenantPayments = array_map(function ($payment) use ($tenantContract) {
            $payOffDate = $payment['payOffDate'];
            $subject = $payment['subject'];
            $amount = -($payment['amount']);
            $comment = $payment['comment'];

            return new TenantPayment([
                'tenant_contract_id' => $tenantContract->id,
                'due_time' => $payOffDate,
                'subject' => $subject,
                'amount' => $amount,
                'is_charge_off_done' => true,
                'charge_off_date' => $payOffDate,
                'collected_by' => '公司',
                'is_visible_at_report' =>false,
                'is_pay_off' => true,
                'comment' => $comment,
            ]);
        }, $payments);

        $tenantContract->tenantPayments()->saveMany($tenantPayments);
        return response()->json(true);
    }
}
