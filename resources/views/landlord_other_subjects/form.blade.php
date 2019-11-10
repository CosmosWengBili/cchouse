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
                    <form action="{{$action}}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method($method)

                        <h3 class="mt-3">基本資料</h3>
                        <table class="table table-bordered">
                            <tbody>
                                <tr>
                                    <td>@lang("model.LandlordOtherSubject.subject")</td>
                                    <td>
                                        <select
                                            data-toggle="selectize"
                                            data-table="landlord_other_subjects"
                                            data-text="subject"
                                            data-value="subject"
                                            data-selected="{{ isset($data["subject"]) ? $data['subject'] : '' }}"
                                            name="subject"
                                            class="form-control form-control-sm"
                                        >
                                        </select>
                                    </td>
                                    <td>@lang("model.LandlordOtherSubject.subject_type")</td>
                                    <td>
                                        <select
                                            data-toggle="selectize"
                                            data-table="landlord_other_subjects"
                                            data-text="subject_type"
                                            data-value="subject_type"
                                            data-selected="{{ isset($data["subject_type"]) ? $data['subject'] : '' }}"
                                            name="subject_type"
                                            class="form-control form-control-sm"
                                        >
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td>@lang("model.LandlordOtherSubject.income_or_expense")</td>
                                    <td>
                                        <select
                                            name="income_or_expense"
                                            class="form-control form-control-sm"
                                        >
                                            @foreach(config('enums.landlord_other_subjects.income_or_expense') as $text)
                                                <option
                                                    value="{{ $text }}"
                                                    {{ isset($data['income_or_expense']) && $data['income_or_expense'] === $text ? 'selected': '' }}
                                                >
                                                    {{ $text }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>@lang("model.LandlordOtherSubject.expense_date")</td>
                                    <td>
                                        <input
                                            class="form-control form-control-sm"
                                            type="date"
                                            name="expense_date"
                                            value="{{ $data['expense_date'] ?? '' }}"
                                        />
                                    </td>
                                </tr>
                                <tr>
                                    <td>@lang("model.LandlordOtherSubject.amount")</td>
                                    <td>
                                        <input
                                            class="form-control form-control-sm"
                                            type="number"
                                            name="amount"
                                            min="1"
                                            step="1"
                                            value="{{ $data['amount'] ?? '' }}"
                                        />
                                    </td>
                                    <td>@lang("model.LandlordOtherSubject.room_id")</td>
                                    <td>
                                        <select
                                            data-toggle="selectize"
                                            data-table="rooms"
                                            data-text="room_code"
                                            data-selected="{{ isset($data["room_id"]) ? $data['room_id'] : '0' }}"
                                            name="room_id"
                                            class="form-control form-control-sm"
                                        >
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td>@lang("model.LandlordOtherSubject.comment")</td>
                                    <td colspan="3">
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
    <script id="set_room_id">
        const qs = window.myQueryString();
        const roomId = qs.getQueryStrings()['room_id'];
        const $roomId = $('[name="room_id"]');
        roomId && $roomId.attr('data-selected', roomId)
    </script>
    <script id="validation">

        $(document).ready(function () {

            const rules = {
                subject: {
                    required: true
                },
                subject_type: {
                    required: true
                },
                income_or_expense: {
                    required: true
                },
                expense_date: {
                    required: true
                },
                amount: {
                    required: true,
                },
                room_id: {
                    required: true
                },
            };

            const messages = {
                subject: {
                    required: '必須輸入'
                },
                subject_type: {
                    required: '必須輸入'
                },
                income_or_expense: {
                    required: '必須輸入'
                },
                expense_date: {
                    required: '必須輸入'
                },
                amount: {
                    required: '必須輸入',
                },
                room_id: {
                    required: '必須輸入'
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
