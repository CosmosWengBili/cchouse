<?php

namespace App\Http\Controllers;

use App\SystemVariable;
use Illuminate\Http\Request;

class SystemVariableController extends Controller
{
    public function index()
    {
        $groups = SystemVariable::groups();
        return view('system_variables.index', ['groups' => $groups]);
    }

    public function edit(string $group)
    {
        $defaultVariables = array_filter(SystemVariable::VARIABLES, function (
            $variable
        ) use ($group) {
            return $variable['group'] == $group;
        });
        $codes = array_map(function ($group) {
            return $group['code'];
        }, $defaultVariables);
        $existedVariables = SystemVariable::whereIn('code', $codes)
            ->get()
            ->toArray();
        $codeToValue = $this->buildKeyValueArray(
            $existedVariables,
            'code',
            'value'
        );
        $codeToOrder = $this->buildKeyValueArray(
            $existedVariables,
            'code',
            'order'
        );

        return view('system_variables.edit', [
            'group' => $group,
            'defaultVariables' => $defaultVariables,
            'codeToValue' => $codeToValue,
            'codeToOrder' => $codeToOrder
        ]);
    }

    public function update(Request $request, string $group)
    {
        $systemVariables = $request->input('system_variables');

        foreach ($systemVariables as $code => $attributes) {
            $scope = ['group' => $group, 'code' => $code];
            $existed = SystemVariable::where($scope)->first();
            if (!is_null($existed)) {
                $existed->update($attributes);
            } else {
                SystemVariable::create(array_merge($scope, $attributes));
            }
        }

        return redirect()->route('system_variables.edit', ['group' => $group]);
    }

    private function buildKeyValueArray(
        array $instances,
        string $keyKey,
        string $valueKey
    ): array {
        $result = [];
        foreach ($instances as $instance) {
            $result[$instance[$keyKey]] = $instance[$valueKey];
        }
        return $result;
    }
}
