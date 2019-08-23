@extends('layouts.app')

@php
    $tenantContracts = \App\TenantContract::pluck('id');
    $subjects = \App\TenantPayment::pluck('subject');
    $users = \App\User::pluck('name', 'id');
@endphp

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
                                    <td>租客合約</td>
                                    <td>
                                        <select
                                            class="form-control form-control-sm"
                                            name="tenant_contract_id"
                                        >
                                            @foreach($tenantContracts as $value)
                                                <option
                                                    value="{{$value}}"
                                                    {{ ($data['tenant_contract_id'] ?? '') == $value ? 'selected' : '' }}
                                                >
                                                    {{$value}}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td>科目</td>
                                    <td>
                                        <select
                                            class="form-control form-control-sm"
                                            name="subject"
                                        >
                                            @foreach($subjects as $subject)
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
                                    <td>應繳時間</td>
                                    <td>
                                        <input
                                            type="date"
                                            name="due_time"
                                            class="form-control form-control-sm"
                                            value="{{ $data['due_time'] ?? '' }}"
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
                                    <td>是否已沖銷</td>
                                    <td>
                                        <input type="hidden" value="0" name="is_charge_off_done"/>
                                        <input
                                            type="checkbox"
                                            name="is_charge_off_done"
                                            value="1"
                                            {{ ($data['sealed_registered'] ?? false) ? 'checked' : '' }}
                                        />
                                    </td>
                                </tr>
                                <tr>
                                    <td>沖銷日期</td>
                                    <td>
                                        <input
                                            type="date"
                                            name="charge_off_date"
                                            class="form-control form-control-sm"
                                            value="{{ $data['charge_off_date'] ?? '' }}"
                                        />
                                    </td>
                                </tr>
                                <tr>
                                    <td>發票號碼</td>
                                    <td>
                                        <input
                                            type="text"
                                            name="invoice_serial_number"
                                            class="form-control form-control-sm"
                                            value="{{ $data['invoice_serial_number'] ?? '' }}"
                                        />
                                    </td>
                                </tr>
                                <tr>
                                    <td>收取者</td>
                                    <td>
                                        <select
                                            class="form-control form-control-sm"
                                            name="collected_by"
                                        >
                                            @foreach($users as $id => $name)
                                                <option
                                                    value="{{$id}}"
                                                    {{ ($data['collected_by'] ?? '') == $id ? 'selected' : '' }}
                                                >
                                                    {{$name}}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td>是否顯示在報表</td>
                                    <td>
                                        <input type="hidden" value="0" name="is_visible_at_report"/>
                                        <input
                                            type="checkbox"
                                            name="is_visible_at_report"
                                            value="1"
                                            {{ ($data['is_visible_at_report'] ?? false) ? 'checked' : '' }}
                                        />
                                    </td>
                                </tr>
                                <tr>
                                    <td>是否為點交</td>
                                    <td>
                                        <input type="hidden" value="0" name="is_pay_off"/>
                                        <input
                                            type="checkbox"
                                            name="is_pay_off"
                                            value="1"
                                            {{ ($data['is_pay_off'] ?? false) ? 'checked' : '' }}
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

<script>
    $(document).ready(function() {
        $('form select').select2();
    });
</script>
@endsection