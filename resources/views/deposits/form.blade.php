@php
    $tenantContractId = Request::get('tenantContractId')?? $data['tenant_contract_id'] ?? null;
@endphp

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
                    <form action="{{$action}}" method="POST">
                        @csrf
                        @method($method)

                        <h3 class="mt-3">基本資料</h3>
                        <table class="table table-bordered">
                            <tbody>
                                <tr>
                                    <td>@lang("model.Deposit.room_id")</td>
                                    <td>
                                        <select
                                            data-toggle="selectize"
                                            data-table="rooms"
                                            data-text="room_code"
                                            data-selected="{{ $data['room_id'] ?? 0 }}"
                                            name="room_id"
                                            class="form-control form-control-sm"
                                        >
                                        </select>
                                    </td>
                                    <td>@lang("model.Deposit.tenant_contract_id")</td>
                                    <td>
                                        <select
                                            data-toggle="selectize"
                                            data-table="tenant_contract"
                                            data-text="id"
                                            data-selected="{{$tenantContractId}}"
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
                                    <td>@lang("model.Deposit.invoicing_amount")</td>
                                    <td>
                                        <input
                                            class="form-control form-control-sm"
                                            type="number"
                                            name="invoicing_amount"
                                            value="{{ $data['invoicing_amount'] ?? '' }}"
                                        />
                                    </td>
                                    <td>@lang("model.Deposit.payer_name")</td>
                                    <td>
                                        <input
                                            class="form-control form-control-sm"
                                            type="text"
                                            name="payer_name"
                                            value="{{ $data['payer_name'] ?? '' }}"
                                        />
                                    </td>
                                </tr>
                                <tr>
                                    <td>@lang("model.Deposit.payer_certification_number")</td>
                                    <td>
                                        <input
                                            class="form-control form-control-sm"
                                            type="text"
                                            name="payer_certification_number"
                                            value="{{ $data['payer_certification_number'] ?? '' }}"
                                        />
                                    </td>
                                    <td>@lang("model.Deposit.payer_is_legal_person")</td>
                                    <td>
                                        <input type="hidden" value="0" name="payer_is_legal_person"/>
                                        <input
                                            type="checkbox"
                                            name="payer_is_legal_person"
                                            value="1"
                                            {{ ($data["payer_is_legal_person"] ?? false) ? 'checked' : '' }}
                                        />
                                    </td>
                                </tr>
                                <tr>
                                    <td>@lang("model.Deposit.payer_phone")</td>
                                    <td>
                                        <input
                                            class="form-control form-control-sm"
                                            type="text"
                                            name="payer_phone"
                                            value="{{ $data['payer_phone'] ?? '' }}"
                                        />
                                    </td>
                                    <td>@lang("model.Deposit.receiver")</td>
                                    <td>
                                        <select
                                            data-toggle="selectize"
                                            data-table="users"
                                            data-text="name"
                                            data-selected="{{ isset($data["confirm_by"]) ? $data['confirm_by'] : '0' }}"
                                            name="receiver"
                                            class="form-control form-control-sm"
                                        >
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td>@lang("model.Deposit.appointment_date")</td>
                                    <td>
                                        <input
                                            class="form-control form-control-sm"
                                            type="date"
                                            name="appointment_date"
                                            value="{{ $data['appointment_date'] ?? '' }}"
                                        />
                                    </td>
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
                                    <td colspan="3">
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
    <script id="validation">

        $(document).ready(function () {

            const rules = {
                deposit_collection_date: {
                    required: true
                },
                deposit_collection_serial_number: {
                    required: true
                },
                invoicing_amount: {
                    required: true,
                }
            };

            const messages = {
                deposit_collection_date: {
                    required: '必須輸入'
                },
                deposit_collection_serial_number: {
                    required: '必須輸入'
                },
                invoicing_amount: {
                    required: '必須輸入',
                }
            };

            $('form').validate({
                rules: rules,
                messages: messages,
                errorElement: "em",
                errorPlacement: function ( error, element ) {
                    error.addClass( "invalid-feedback" );
                    if ( element.prop( "type" ) === "checkbox" ) {
                        error.insertAfter( element.next( "label" ) );
                    } else {
                        error.insertAfter( element );
                    }
                },
                highlight: function ( element, errorClass, validClass ) {
                    $( element ).addClass( "is-invalid" ).removeClass( "is-valid" );
                },
                unhighlight: function (element, errorClass, validClass) {
                    $( element ).addClass( "is-valid" ).removeClass( "is-invalid" );
                }
            });

        });
    </script>
@endsection
