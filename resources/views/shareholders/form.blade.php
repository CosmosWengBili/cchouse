@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8 mt-5">
            <div class="card">
                <div class="card-body">
                    <div class="card-title">
                        新建 / 編輯表單
                    </div>
                    <form action="{{$action}}" method="POST">
                        @csrf
                        @method($method)

                        <h3 class="mt-3">基本資料</h3>
                        <table class="table table-bordered">
                            <tbody>
                            <tr>
                                <td>@lang("model.Shareholder.name")</td>
                                <td>
                                    <input
                                        class="form-control form-control-sm"
                                        type="text"
                                        name="name"
                                        value="{{ isset($data["name"]) ? $data['name'] : '' }}"
                                    />
                                </td>
                            </tr>

                            <tr>
                                <td>@lang("model.Shareholder.email")</td>
                                <td>
                                    <input
                                        class="form-control form-control-sm"
                                        type="text"
                                        name="email"
                                        value="{{ isset($data["email"]) ? $data['email'] : '' }}"
                                    />
                                </td>
                            </tr>
                            <tr>
                                <td>@lang("model.Shareholder.is_remittance_fee_collected")</td>
                                <td>
                                    {{-- unchecked value for checkbox--}}
                                    <input type="hidden" value="0" name="is_remittance_fee_collected"/>
                                    <input
                                        type="checkbox"
                                        name="is_remittance_fee_collected"
                                        value="1"
                                        {{ isset($data["is_remittance_fee_collected"]) ? ($data['is_remittance_fee_collected'] ? 'checked' : '') : '' }}
                                    />
                                </td>
                            </tr>

                            <tr>
                                <td>@lang("model.Shareholder.bank_name")</td>
                                <td>
                                    <input
                                        class="form-control form-control-sm"
                                        type="text"
                                        name="bank_name"
                                        value="{{ isset($data["bank_name"]) ? $data['bank_name'] : '' }}"
                                    />
                                </td>
                            </tr>

                            <tr>
                                <td>@lang("model.Shareholder.bank_code")</td>
                                <td>
                                    <input
                                        class="form-control form-control-sm"
                                        type="text"
                                        name="bank_code"
                                        value="{{ isset($data["bank_code"]) ? $data['bank_code'] : '' }}"
                                    />
                                </td>
                            </tr>

                            <tr>
                                <td>@lang("model.Shareholder.account_number")</td>
                                <td>
                                    <input
                                        class="form-control form-control-sm"
                                        type="text"
                                        name="account_number"
                                        value="{{ isset($data["account_number"]) ? $data['account_number'] : '' }}"
                                    />
                                </td>
                            </tr>
                            <tr>
                                <td>@lang("model.Shareholder.account_name")</td>
                                <td>
                                    <input
                                        class="form-control form-control-sm"
                                        type="text"
                                        name="account_name"
                                        value="{{ isset($data["account_name"]) ? $data['account_name'] : '' }}"
                                    />
                                </td>
                            </tr>
                            <tr>
                                <td>@lang("model.Shareholder.transfer_from")</td>
                                <td>
                                    <select
                                        class="form-control form-control-sm"
                                        name="transfer_from"
                                        value="{{ $data['transfer_from'] ?? '' }}"
                                    />
                                        @foreach(config('enums.shareholders.transfer_from') as $value)
                                            <option value="{{$value}}">{{$value}}</option>
                                        @endforeach
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td>@lang("model.Shareholder.bill_delivery")</td>
                                <td>
                                    <input
                                        class="form-control form-control-sm"
                                        type="text"
                                        name="bill_delivery"
                                        value="{{ isset($data["bill_delivery"]) ? $data['bill_delivery'] : '' }}"
                                    />
                                </td>
                            </tr>
                            <tr>
                                <td>@lang("model.Shareholder.distribution_method")</td>
                                <td>
                                    <select
                                        class="form-control form-control-sm"
                                        name="distribution_method"
                                        value="{{ $data['distribution_method'] ?? '' }}"
                                    />
                                        @foreach(config('enums.shareholders.distribution_method') as $value)
                                            <option value="{{$value}}">{{$value}}</option>
                                        @endforeach
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td>@lang("model.Shareholder.distribution_start_date")</td>
                                <td>
                                    <input
                                        class="form-control form-control-sm"
                                        type="date"
                                        name="distribution_start_date"
                                        value="{{ isset($data["distribution_start_date"]) ? $data['distribution_start_date'] : '' }}"
                                    />
                                </td>
                            </tr>
                            <tr>
                                <td>@lang("model.Shareholder.distribution_end_date")</td>
                                <td>
                                    <input
                                        class="form-control form-control-sm"
                                        type="date"
                                        name="distribution_end_date"
                                        value="{{ isset($data["distribution_end_date"]) ? $data['distribution_end_date'] : '' }}"
                                    />
                                </td>
                            </tr>
                            <tr>
                                <td>@lang("model.Shareholder.distribution_rate")</td>
                                <td>
                                    <input
                                        class="form-control form-control-sm"
                                        type="number"
                                        name="distribution_rate"
                                        value="{{ isset($data["distribution_rate"]) ? $data['distribution_rate'] : '' }}"
                                    />
                                </td>
                            </tr>
                            <tr>
                                <td>@lang("model.Shareholder.investment_amount")</td>
                                <td>
                                    <input
                                        class="form-control form-control-sm"
                                        type="number"
                                        name="investment_amount"
                                        value="{{ isset($data["investment_amount"]) ? $data['investment_amount'] : '' }}"
                                    />
                                </td>
                            </tr>
                            <tr>
                                <td>@lang("model.Shareholder.building_ids")</td>
                                <td>
                                    <input
                                        class="form-control form-control-sm"
                                        type="text"
                                        name="building_ids"
                                        value="{{ isset($data["building_ids"]) ? $data['building_ids'] : '' }}"
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
$('[name="building_ids"]').selectize({
    delimiter: ',',
    persist: false,
    create: function(input) {
        return {
            value: input,
            text: input
        }
    }
});
</script>
@endsection
