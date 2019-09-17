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
                                <td>@lang("model.LandlordPayment.room_id")</td>
                                <td>
                                    <select
                                        data-toggle="selectize"
                                        data-table="rooms"
                                        data-text="id"
                                        data-selected="{{ isset($data["room_id"]) ? $data['room_id'] : '0' }}"
                                        name="room_id"
                                        class="form-control form-control-sm"
                                    >
                                    </select>
                                </td>
                                <td>@lang("model.LandlordPayment.subject")</td>
                                <td>
                                    <select
                                        data-toggle="selectize"
                                        data-table="landlord_payments"
                                        data-text="subject"
                                        data-value="subject"
                                        data-selected="{{ isset($data["subject"]) ? $data['subject'] : '0' }}"
                                        name="subject"
                                        class="form-control form-control-sm"
                                    >
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td>@lang("model.LandlordPayment.bill_serial_number")</td>
                                <td>
                                    <input
                                        class="form-control form-control-sm"
                                        type="text"
                                        name="bill_serial_number"
                                        value="{{ isset($data["bill_serial_number"]) ? $data['bill_serial_number'] : '' }}"
                                    />
                                </td>
                                <td>@lang("model.LandlordPayment.bill_start_date")</td>
                                <td>
                                    <input
                                        class="form-control form-control-sm"
                                        type="date"
                                        name="bill_start_date"
                                        value="{{ isset($data["bill_start_date"]) ? $data['bill_start_date'] : '' }}"
                                    />
                                </td>
                            </tr>
                            <tr>
                                <td>@lang("model.LandlordPayment.bill_end_date")</td>
                                <td>
                                    <input
                                        class="form-control form-control-sm"
                                        type="date"
                                        name="bill_end_date"
                                        value="{{ isset($data["bill_end_date"]) ? $data['bill_end_date'] : '' }}"
                                    />
                                </td>
                                <td>@lang("model.LandlordPayment.collection_date")</td>
                                <td>
                                    <input
                                        class="form-control form-control-sm"
                                        type="date"
                                        name="collection_date"
                                        value="{{ isset($data["collection_date"]) ? $data['collection_date'] : '' }}"
                                    />
                                </td>
                            </tr>

                            <tr>
                                <td>@lang("model.LandlordPayment.billing_vendor")</td>
                                <td>
                                    <input
                                        class="form-control form-control-sm"
                                        type="text"
                                        name="billing_vendor"
                                        value="{{ isset($data["billing_vendor"]) ? $data['billing_vendor'] : '' }}"
                                    />
                                </td>
                                <td>@lang("model.LandlordPayment.amount")</td>
                                <td>
                                    <input
                                        class="form-control form-control-sm"
                                        type="text"
                                        name="amount"
                                        value="{{ isset($data["amount"]) ? $data['amount'] : '' }}"
                                    />
                                </td>
                            </tr>

                            <tr>
                                <td>@lang("model.LandlordPayment.comment")</td>
                                <td>
                                    <input
                                        class="form-control form-control-sm"
                                        type="text"
                                        name="comment"
                                        value="{{ isset($data["comment"]) ? $data['comment'] : '' }}"
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
                bill_serial_number: {
                    required: true
                },
                bill_start_date: {
                    required: true
                },
                bill_end_date: {
                    required: true
                },
                collection_date: {
                    required: true
                },
                billing_vendor: {
                    required: true,
                },
                amount: {
                    required: true,
                },};

            const messages = {
                bill_serial_number: {
                    required: '必須輸入'
                },
                bill_start_date: {
                    required: '必須輸入'
                },
                bill_end_date: {
                    required: '必須輸入'
                },
                collection_date: {
                    required: '必須輸入'
                },
                billing_vendor: {
                    required: '必須輸入',
                },
                amount: {
                    required: '必須輸入',
                },
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
