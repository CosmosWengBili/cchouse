<?php

namespace App\Http\Controllers;

use App\Responser\NestedRelationResponser;
use App\Services\NestedToAttributeService;
use App\Tenant;
use App\TenantContract;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TenantPaymentController extends Controller
{
    public function index(Request $request) {
        $by = $request->input('by');

        if ($by == 'time') {
            return $this->indexByTime($request);
        }
        return $this->indexByContract($request);
    }


    private function indexByTime(Request $request) {
        return $this->indexByContract($request);
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

        return view('tenant_payments.index_by_contract', array_merge($data, ['by' => 'contract']));
    }
}
