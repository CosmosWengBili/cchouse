<?php

namespace App\Http\Controllers;

use App\Deposit;
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
    const MAIN_TYPES = [
        TenantPayment::class,
        TenantElectricityPayment::class,
        Deposit::class,
    ];

    public function index(Request $request)
    {
        $type = $request->input('type');
        $records = ReversalErrorCase::leftJoin('pay_logs', 'pay_logs.id', '=', 'reversal_error_cases.pay_log_id')
            ->select('reversal_error_cases.*');
        if ($type == 'other') {
            $records = $records->whereNotIn('pay_logs.loggable_type', self::MAIN_TYPES)->with('payLog');
        } else {
            $records = $records->whereIn('pay_logs.loggable_type', self::MAIN_TYPES)->with('payLog.loggable');
        }
        $cases = $this->limitRecords($records)->makeHidden(['payLog']);;

        return view('reversal_error_cases.index', [
            'type' => $type,
            'data' => ['reversal_error_cases' => $cases],
            'relations' => [],
            'model_name' => null,
        ]);
    }

    public function pass(Request $request, $id)
    {
        $reversalErrorCase = ReversalErrorCase::find($id);
        $reversalErrorCase->status = '已結案';
        $reversalErrorCase->save();

        return response()->json(true);
    }
}
