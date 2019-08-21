@extends('layouts.app')

@php
    $tenantContracts = \App\TenantContract::pluck('id');
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

    (function() {
        let method = null;
        let pricePerDegree = 0;
        let pricePerDegreeSummer = 0;
        const $tenantContractIdInput = $('select[name="tenant_contract_id"]');

        function calculatePrice() {
            if (method !== '固定') return;

            const startOf110v = Number($('input[name="110v_start_degree"]').val());
            const endOf110v = Number($('input[name="110v_end_degree"]').val());
            const startOf220v = Number($('input[name="220v_start_degree"]').val());
            const endOf220v = Number($('input[name="220v_end_degree"]').val());
            const readDate = new Date($('input[name="ammeter_read_date"]').val());
            const readMonth = readDate.getMonth() + 1;
            const ratio = [7, 8, 9, 10].includes(readMonth) ? pricePerDegreeSummer : pricePerDegree;
            const amount = Math.round((endOf110v + endOf220v - startOf110v - startOf220v) * ratio);

            console.log(method, pricePerDegree, pricePerDegreeSummer);

            $('input[name="amount"]').val(amount);
        }
        function getDegree() {
            const tenantContractsId = $tenantContractIdInput.val();
            $.get('/tenantContracts/' + tenantContractsId + '/electricityDegree', function (data) {
                method = data.method;
                pricePerDegree = data.pricePerDegree;
                pricePerDegreeSummer = data.pricePerDegreeSummer;

                calculatePrice();
            });
        }

        $tenantContractIdInput.on('change', getDegree);

        $(
          'input[name="110v_start_degree"],' +
          'input[name="110v_end_degree"],' +
          'input[name="220v_start_degree"],' +
          'input[name="220v_end_degree"],' +
          'input[name="ammeter_read_date"]'
        ).on('change', calculatePrice);
        getDegree();
    })();
</script>
@endsection
