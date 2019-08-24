<?php

namespace App\Http\Controllers;

use App\CompanyIncome;
use App\Responser\FormDataResponser;
use Illuminate\Http\Request;

class CompanyIncomeController extends Controller
{
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
