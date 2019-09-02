<?php

namespace App\Http\Controllers;

use App\CompanyIncome;
use App\Responser\FormDataResponser;
use App\Responser\NestedRelationResponser;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CompanyIncomeController extends Controller
{
    public function index() {
        $endAt = Carbon::now();
        $startAt = $endAt->copy()->subMonth(5)->startOfMonth(); // 近六個月（含本月）

        $companyIncomes = CompanyIncome::whereBetween('income_date', [$startAt, $endAt])
                    ->get()
                    ->groupBy(function ($companyIncome) {
                        return $companyIncome->income_date->month;
                    })
                    ->toArray();

        return view('company_incomes.index', ['companyIncomes' => $companyIncomes]);
    }

    public function show(Request $request, CompanyIncome $companyIncome) {
        $responseData = new NestedRelationResponser();
        $responseData
            ->show($companyIncome->load($request->withNested))
            ->relations($request->withNested);

        return view('company_incomes.show', $responseData->get());
    }

    public function create() {
        $responser = new FormDataResponser();
        $data = $responser->create(CompanyIncome::class, 'companyIncomes.store')->get();

        return view('company_incomes.form', $data);
    }

    public function edit(CompanyIncome $companyIncome) {
        $responser = new FormDataResponser();
        $data = $responser->edit($companyIncome, 'companyIncomes.update')->get();

        return view('company_incomes.form', $data);
    }

    public function store(Request $request) {
        $validatedData = $this->fetchValidateData($request);
        CompanyIncome::create($validatedData);

        return redirect()->route('companyIncomes.index');
    }

    public function update(Request $request, CompanyIncome $companyIncome) {
        $validatedData = $this->fetchValidateData($request);
        $companyIncome->update($validatedData);

        return redirect()->route('companyIncomes.index');
    }

    private function fetchValidateData(Request $request) {
        return $request->validate([
            'tenant_contract_id' => 'required',
            'subject' => 'required',
            'income_date' => 'required',
            'amount' => 'required',
            'comment' => 'required',
        ]);
    }
}
