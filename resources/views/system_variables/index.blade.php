@extends('layouts.app')

@php
$systemVariables = \App\SystemVariable::variables();
@endphp

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12 mt-5">
            <div class="card">
                <div class="card-body">
                    <div class="card-title">
                        系統變數
                    </div>
                    @foreach($groups as $group)
                        <a
                            href="{{ route('system_variables.edit', [ 'system_variable' => $group ]) }}"
                            class="py-3 m-3 font-weight-bold btn btn-lg btn-danger"
                        >
                            @lang("general.".strtolower($group))
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
