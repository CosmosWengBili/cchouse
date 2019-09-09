@extends('layouts.app')

@section('content')
    @include('layouts.form_error')

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
                                    <td>類型</td>
                                    <td>
                                        <select
                                            class="form-control form-control-sm"
                                            name="loggable_type"
                                        >
                                            <option
                                                value="{{App\TenantElectricityPayment::class}}"
                                                {{ ($data['loggable_type'] ?? '') == App\TenantElectricityPayment::class ? 'selected' : '' }}
                                            >
                                                電費
                                            </option>
                                            <option
                                                value="{{App\TenantPayment::class }}"
                                                {{ ($data['loggable_type'] ?? '') == App\TenantPayment::class ? 'selected' : '' }}
                                            >
                                                租金雜費
                                            </option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td>費用 ID</td>
                                    <td>
                                        <input
                                            type="number"
                                            name="loggable_id"
                                            class="form-control form-control-sm"
                                            value="{{ $data['loggable_id'] ?? '' }}"
                                        />
                                    </td>
                                </tr>
                                <tr>
                                    <td>科目</td>
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
                                    <td>繳費類別</td>
                                    <td>
                                        <select
                                            class="form-control form-control-sm"
                                            name="payment_type"
                                        >
                                            @foreach(config('enums.pay_logs.payment_type') as $paymentType)
                                                <option
                                                    value="{{$paymentType}}"
                                                    {{ ($data['payment_type'] ?? '') == $paymentType ? 'selected' : '' }}
                                                >
                                                    {{$paymentType}}
                                                </option>
                                            @endforeach
                                        </select>
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
                                    <td>虛擬帳號</td>
                                    <td>
                                        <input
                                            type="text"
                                            name="virtual_account"
                                            class="form-control form-control-sm"
                                            value="{{ $data['virtual_account'] ?? '' }}"
                                        />
                                    </td>
                                </tr>
                                <tr>
                                    <td>匯款時間</td>
                                    <td>
                                        @php
                                            $paid_at = '';
                                            isset($data['paid_at']) ?? $paid_at = (new \Carbon\Carbon($data['paid_at']))->format('Y-m-d');
                                        @endphp
                                        <input
                                            type="date"
                                            name="paid_at"
                                            class="form-control form-control-sm"
                                            value="{{ $paid_at }}"
                                        />
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
