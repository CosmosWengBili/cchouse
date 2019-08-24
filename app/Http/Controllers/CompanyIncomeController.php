<?php

namespace App\Http\Controllers;

use App\CompanyIncome;
use App\Responser\FormDataResponser;

class CompanyIncomeController extends Controller
{
    public function create() {
        $responser = new FormDataResponser();
        $data = $responser->create(CompanyIncome::class, 'companyIncomes.store')->get();

        return view('company_incomes.form', $data);
    }
}
