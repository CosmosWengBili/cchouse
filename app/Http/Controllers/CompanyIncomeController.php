<?php

namespace App\Http\Controllers;

use App\CompanyIncome;
use App\Responser\FormDataResponser;
use App\Responser\NestedRelationResponser;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CompanyIncomeController extends Controller
{
    public function index()
    {
        $endAt = Carbon::now();
        $startAt = $endAt->copy()->subMonth(5)->startOfMonth(); // 近六個月（含本月）
        $selectColumns = array_merge(['company_incomes.*'], CompanyIncome::extraInfoColumns());
        $selectStr = DB::raw(join(', ', $selectColumns));
        $companyIncomes = CompanyIncome::withExtraInfo()->with(['receipts:id,invoice_serial_number'])
            ->whereBetween('income_date', [$startAt, $endAt])
            ->select($selectStr)
            ->get()
            ->groupBy(function ($companyIncome) {
                return $companyIncome->income_date->month;
            })
            ->toArray();

        // unset receipts and take invoice_serial_number out only
        foreach ($companyIncomes as &$items) {
            foreach ($items as &$companyIncome) {
                if (! (isset($companyIncome['receipts']) && count($companyIncome['receipts']) > 0)) {
                    unset($companyIncome['receipts']);
                    $companyIncome['invoice_serial_number'] = '';
                    continue;
                }

                $receipts = $companyIncome['receipts'];
                unset($companyIncome['receipts']);

                $invoice_serial_number = [];
                foreach ($receipts as $receipt) {
                    $invoice_serial_number[] = $receipt['invoice_serial_number'];
                }
                $companyIncome['invoice_serial_number'] = join(',', $invoice_serial_number);
            }
        }

        return view('company_incomes.index', ['companyIncomes' => $companyIncomes]);
    }

    public function show(Request $request, CompanyIncome $companyIncome)
    {
        $responseData = new NestedRelationResponser();
        $responseData
            ->show($companyIncome->load($request->withNested))
            ->relations($request->withNested);

        return view('company_incomes.show', $responseData->get());
    }

    public function create()
    {
        $responser = new FormDataResponser();
        $data = $responser->create(CompanyIncome::class, 'companyIncomes.store')->get();

        return view('company_incomes.form', $data);
    }

    public function edit(CompanyIncome $companyIncome)
    {
        $responser = new FormDataResponser();
        $data = $responser->edit($companyIncome, 'companyIncomes.update')->get();

        return view('company_incomes.form', $data);
    }

    public function store(Request $request)
    {
        $validatedData = $this->fetchValidateData($request);
        CompanyIncome::create($validatedData);

        return redirect($request->_redirect);
    }

    public function update(Request $request, CompanyIncome $companyIncome)
    {
        $validatedData = $this->fetchValidateData($request);
        $companyIncome->update($validatedData);

        return redirect($request->_redirect);
    }

    private function fetchValidateData(Request $request)
    {
        return $request->validate([
            'tenant_contract_id' => 'required|exists:tenant_contract,id',
            'subject' => 'required',
            'income_date' => 'required',
            'amount' => 'required',
            'comment' => 'required',
        ]);
    }
}
