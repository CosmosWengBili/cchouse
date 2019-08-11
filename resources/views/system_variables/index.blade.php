@extends('layouts.app')

@php
$systemVariables = \App\SystemVariable::VARIABLES;
@endphp

@section('content')
@<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8 mt-5">
            <div class="card">
                <div class="card-body">
                    <div class="card-title">
                        系統變數
                    </div>
                    <form action="{{ route('system_variables.store') }}" method="POST">
                        @csrf
                        @method('post')
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>名稱</th>
                                    <th>Code</th>
                                    <th>值</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($systemVariables as $systemVariable)
                                <tr>
                                    <td>{{ $systemVariable['name'] }}</td>
                                    <td>{{ $systemVariable['code'] }}</td>
                                    <td>
                                        <input
                                            class="form-control form-control-sm"
                                            type="text"
                                            name="system_variables[{{ $systemVariable['code'] }}]"
                                            value="{{ isset($codeToValue[$systemVariable['code']]) ? $codeToValue[$systemVariable['code']] : $systemVariable['defaultValue'] }}"
                                        />
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        <button class="mt-5 btn btn-success" type="submit">更新</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
