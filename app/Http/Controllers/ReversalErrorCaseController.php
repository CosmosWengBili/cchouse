<?php

namespace App\Http\Controllers;

use App\ReversalErrorCase;
use App\Services\InvoiceService;
use App\Responser\FormDataResponser;
use App\Responser\NestedRelationResponser;
use App\TenantContract;
use App\TenantElectricityPayment;
use App\TenantPayment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class ReversalErrorCaseController extends Controller
{
    public function index(Request $request) {
      $responseData = new NestedRelationResponser();
      $responseData
          ->index(
              'reversal_error_cases',
              $this->limitRecords(ReversalErrorCase::with($request->withNested))
          )
          ->relations($request->withNested);

      return view('reversal_error_cases.index', $responseData->get());
    }

    public function pass(Request $request, $id){
      $reversalErrorCase = ReversalErrorCase::find($id);
      $reversalErrorCase->status = '已結案';
      $reversalErrorCase->save();

      return response()->json(true);
    }
}