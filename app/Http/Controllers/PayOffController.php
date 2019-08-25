<?php

namespace App\Http\Controllers;

use App\PayLog;
use App\Responser\FormDataResponser;
use App\Responser\NestedRelationResponser;
use App\Services\PayOffService;
use App\Tenant;
use App\TenantContract;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PayOffController extends Controller
{
    public function index(Request $request) {
        $responseData = new NestedRelationResponser();
        $responseData
            ->index(
                'tenant_contracts',
                TenantContract::with($request->withNested)->active()->get()
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
            'payOffDate' => $payOffDate,
            'payOffData' => $payOffData,
        ]);
    }
}
