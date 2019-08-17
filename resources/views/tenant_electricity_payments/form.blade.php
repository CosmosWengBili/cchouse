@extends('layouts.app')

@php
    $tenantContracts = \App\TenantContract::pluck('id');
@endphp

@section('content')
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
                                    <td>抄表時間</td>
                                    <td>
                                        <input
                                            type="date"
                                            name="ammeter_read_date"
                                            class="form-control form-control-sm"
                                            value="{{ $data['ammeter_read_date'] ?? '' }}"
                                        />
                                    </td>
                                </tr>
                                <tr>
                                    <td>110v起</td>
                                    <td>
                                        <input
                                            type="number"
                                            name="110v_start_degree"
                                            class="form-control form-control-sm"
                                            value="{{ $data['110v_start_degree'] ?? '' }}"
                                        />
                                    </td>
                                </tr>
                                <tr>
                                    <td>110v迄</td>
                                    <td>
                                        <input
                                            type="number"
                                            name="110v_end_degree"
                                            class="form-control form-control-sm"
                                            value="{{ $data['110v_end_degree'] ?? '' }}"
                                        />
                                    </td>
                                </tr>
                                <tr>
                                    <td>220v起</td>
                                    <td>
                                        <input
                                            type="number"
                                            name="220v_start_degree"
                                            class="form-control form-control-sm"
                                            value="{{ $data['220v_start_degree'] ?? '' }}"
                                        />
                                    </td>
                                </tr>
                                <tr>
                                    <td>220v迄</td>
                                    <td>
                                        <input
                                            type="number"
                                            name="220v_end_degree"
                                            class="form-control form-control-sm"
                                            value="{{ $data['220v_end_degree'] ?? '' }}"
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
