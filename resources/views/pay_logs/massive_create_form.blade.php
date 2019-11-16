@extends('layouts.app')

@section('content')
    @include('layouts.form_error')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12 mt-5">
            <div class="card">
                <div class="card-body">
                    <div class="card-title">
                        新建 / 編輯表單
                    </div>
                    <form action="{{ route('payLogs.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="tenant_contract_id" value="{{ $tenantContractId }}" />

                        <table class="table table-bordered">
                            <tbody>
                                <tr>
                                    <td>@lang("model.PayLog.come_from_bank")</td>
                                    <td>
                                        <select
                                            class="form-control form-control-sm"
                                            name="come_from_bank"
                                            required
                                        />
                                            @foreach(config('enums.pay_logs.come_from_bank') as $value)
                                                <option value="{{$value}}">{{$value}}</option>
                                            @endforeach
                                        </select>
                                    </td>

                                    <td>@lang("model.PayLog.pay_sum")</td>
                                    <td>
                                        <input
                                            type="number"
                                            name="pay_sum"
                                            class="form-control form-control-sm"
                                            required="required"
                                        />
                                    </td>
                                </tr>
                                <tr>
                                    <td>@lang("model.PayLog.paid_at")</td>
                                    <td>
                                        <input
                                            type="datetime-local"
                                            name="paid_at"
                                            class="form-control form-control-sm"
                                            required="required"
                                        />
                                    </td>
                                    <td>@lang("model.PayLog.deposit_at")</td>
                                    <td>
                                        <input
                                            type="datetime-local"
                                            name="deposit_at"
                                            class="form-control form-control-sm"
                                            required="required"
                                        />
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <table id="pay-log-detail-table" class="table table-striped">
                            <thead>
                                <tr>
                                    <td>@lang("model.PayLog.loggable_id")</td>
                                    <td>@lang("model.PayLog.amount")</td>
                                    <td>@lang("model.PayLog.comment")</td>
                                    <td></td>
                                </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td colspan="6" class="text-center">
                                    <button class="btn btn-success js-add-detail-row" type="button">新增</button>
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
    $(document).ready(function () {
        var buildTemplate = function (idx) {
            return `
    <tr>
        <td>
            <input type="hidden" name="pay_logs[${idx}][loggable_type]" value="App\\TenantPayment">
            <select name="pay_logs[${idx}][loggable_id]" class="form-control form-control-sm" required="required">
                @foreach($unchargedPayments as $payment)
                    <option value="{{$payment->id}}">{{ $payment->due_time->format('Y-m-d')}} {{ $payment->subject }} {{ $payment->amount }}</option>
                @endforeach
            </select>
        </td>
        <td>
            <input name="pay_logs[${idx}][amount]" type="number" min="0" value="0" class="form-control form-control-sm" required="required" />
        </td>
        <td>
            <input name="pay_logs[${idx}][comment]" class="form-control form-control-sm"/>
        </td>
        <td>
            <div>
                <button class="btn btn-danger btn-xs js-remove-detail-row" type="button">X</button>
            </div>
        </td>
    </tr>
`
        };

        // must to unbind and then rebind
        $(document).off('click', 'button.js-add-detail-row');
        $(document).on('click', 'button.js-add-detail-row', function () {
            var idx = $(this).parents('tbody').find('tr').length;
            var $insertRow = $(this).parents('tbody').find('tr:last-child');
            $(buildTemplate(idx)).insertBefore($insertRow);
        });

        // must to unbind and then rebind
        $(document).off('click', 'button.js-remove-detail-row');
        $(document).on('click', 'button.js-remove-detail-row', function () {
            $(this).closest('tr').remove();
        });
    });
</script>
@endsection
