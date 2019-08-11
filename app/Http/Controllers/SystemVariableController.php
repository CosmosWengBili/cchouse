<?php

namespace App\Http\Controllers;

use App\SystemVariable;
use Illuminate\Http\Request;

class SystemVariableController extends Controller
{
    public function index() {
        $systemVariables = SystemVariable::all();
        $codeToValue = [];
        foreach ($systemVariables as $systemVariable) {
            $codeToValue[$systemVariable->code] = $systemVariable->value;
        }

        return view('system_variables.index', ['codeToValue' => $codeToValue]);
    }

    public function store(Request $request) {
        $systemVariables = $request->input('system_variables');

        foreach ($systemVariables as $code => $value) {
            $existed = SystemVariable::where('code', $code)->first();
            if(!is_null($existed)) {
                $existed->update(['value' => $value]);
            } else {
                SystemVariable::create(['code' => $code, 'value' => $value]);
            }
        }

        return redirect()->route('system_variables.index');
    }
}
