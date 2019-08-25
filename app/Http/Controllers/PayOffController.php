<?php

namespace App\Http\Controllers;

use App\PayLog;
use App\Responser\FormDataResponser;
use App\Responser\NestedRelationResponser;
use App\TenantContract;
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
}
