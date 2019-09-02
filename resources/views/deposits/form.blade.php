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
                                    <td>@lang("model.Deposit.tenant_contract_id")</td>
                                    <td>
                                        <select 
                                            data-toggle="selectize" 
                                            data-table="tenant_contract" 
                                            data-text="id" 
                                            data-selected="{{ $data['tenant_contract_id'] ?? 0 }}"
                                            name="tenant_contract_id"
                                            class="form-control form-control-sm" 
                                        >
                                        </select>
                                        
                                    </td>
                                </tr>
                                <tr>
                                    <td>@lang("model.Deposit.deposit_collection_date")</td>
                                    <td>
                                        <input
                                            class="form-control form-control-sm"
                                            type="date"
                                            name="deposit_collection_date"
                                            value="{{ $data['deposit_collection_date'] ?? '' }}"
                                        />
                                    </td>
                                </tr>
                                <tr>
                                    <td>@lang("model.Deposit.deposit_collection_serial_number")</td>
                                    <td>
                                        <input
                                            class="form-control form-control-sm"
                                            type="text"
                                            name="deposit_collection_serial_number"
                                            value="{{ $data['deposit_collection_serial_number'] ?? '' }}"
                                        />
                                    </td>
                                </tr>
                                <tr>
                                    <td>@lang("model.Deposit.deposit_confiscated_amount")</td>
                                    <td>
                                        <input
                                            class="form-control form-control-sm"
                                            type="number"
                                            name="deposit_confiscated_amount"
                                            value="{{ $data['deposit_confiscated_amount'] ?? '' }}"
                                        />
                                    </td>
                                </tr>
                                <tr>
                                    <td>@lang("model.Deposit.deposit_returned_amount")</td>
                                    <td>
                                        <input
                                            class="form-control form-control-sm"
                                            type="number"
                                            name="deposit_returned_amount"
                                            value="{{ $data['deposit_returned_amount'] ?? '' }}"
                                        />
                                    </td>
                                </tr>
                                <tr>
                                    <td>@lang("model.Deposit.confiscated_or_returned_date")</td>
                                    <td>
                                        <input
                                            class="form-control form-control-sm"
                                            type="date"
                                            name="confiscated_or_returned_date"
                                            value="{{ $data['confiscated_or_returned_date'] ?? '' }}"
                                        />
                                    </td>
                                </tr>
                                <tr>
                                    <td>@lang("model.Deposit.invoicing_amount")</td>
                                    <td>
                                        <input
                                            class="form-control form-control-sm"
                                            type="number"
                                            name="invoicing_amount"
                                            value="{{ $data['invoicing_amount'] ?? '' }}"
                                        />
                                    </td>
                                </tr>
                                <tr>
                                    <td>@lang("model.Deposit.invoice_date")</td>
                                    <td>
                                        <input
                                            class="form-control form-control-sm"
                                            type="date"
                                            name="invoice_date"
                                            value="{{ $data['invoice_date'] ?? '' }}"
                                        />
                                    </td>
                                </tr>
                                <tr>
                                    <td>@lang("model.Deposit.is_deposit_collected")</td>
                                    <td>

                                        <input type="hidden" value="0" name="is_deposit_collected"/>
                                        <input
                                            type="checkbox"
                                            name="is_deposit_collected"
                                            value="1"
                                            {{ isset($data["is_deposit_collected"]) ? ($data['is_deposit_collected'] ? 'checked' : '') : '' }}
                                        />
                                    </td>
                                </tr>
                                <tr>
                                    <td>@lang("model.Deposit.comment")</td>
                                    <td>
                                        <input
                                            class="form-control form-control-sm"
                                            type="text"
                                            name="comment"
                                            value="{{ $data['comment'] ?? '' }}"
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
@endsection
