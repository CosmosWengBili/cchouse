@extends('layouts.app')

@section('content')
@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
@php
    $tenantContractIds = \App\TenantContract::select('id')->pluck('id')->toArray();
@endphp

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8 my-5">
            <div class="card">
                <div class="card-body">
                    <div class="card-title">
                        新建 / 編輯表單
                    </div>
                    <form action="{{$action}}" method="POST">
                        @csrf
                        @method($method)
                        <table class="table table-bordered">
                            <tbody>
                                <tr>
                                    <td>租客合約 ID</td>
                                    <td>
                                        <select class="form-control" name="tenant_contract_id">
                                            @foreach($tenantContractIds as $tenantContractId)
                                                <option
                                                    value="{{$tenantContractId}}"
                                                    {{ ($data['tenant_contract_id'] ?? '') == $tenantContractId ? 'selected' : '' }}
                                                >
                                                    {{$tenantContractId}}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td>項目</td>
                                    <td>
                                        <select
                                            class="form-control form-control-sm"
                                            name="subject"
                                        >
                                            <option value="電費">電費</option>
                                            @foreach(config('enums.tenant_payments.subject') as $subject)
                                                <option
                                                    value="{{$subject}}"
                                                    {{ ($data['subject'] ?? '') == $subject ? 'selected' : '' }}
                                                >
                                                    {{$subject}}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td>收入時間</td>
                                    <td>
                                        <input
                                            type="date"
                                            name="income_date"
                                            class="form-control form-control-sm"
                                            value="{{ $data['income_date'] ?? '' }}"
                                        />
                                    </td>
                                </tr>
                                <tr>
                                    <td>費用</td>
                                    <td>
                                        <input
                                            type="number"
                                            name="amount"
                                            class="form-control form-control-sm"
                                            value="{{ $data['amount'] ?? '' }}"
                                        />
                                    </td>
                                </tr>
                                <tr>
                                    <td>備註</td>
                                    <td>
                                        <textarea name="comment" class="form-control" rows="15">{{  $data['comment'] ?? '' }}</textarea>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <button class="mt-5 btn btn-success" type="submit">送出</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
