@extends('layouts.app')

@section('content')
    @include('layouts.form_error')

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
                                <td>@lang("model.Key.key_name")</td>
                                <td>
                                    <input
                                        class="form-control form-control-sm"
                                        type="text"
                                        name="key_name"
                                        value="{{ isset($data["key_name"]) ? $data['key_name'] : '' }}"
                                    />
                                </td>
                            </tr>
                            <tr>
                                <td>@lang("model.Key.keeper_id")</td>
                                <td>
                                    <select
                                        data-toggle="selectize"
                                        data-table="users"
                                        data-text="name"
                                        data-value="id"
                                        data-selected="{{ isset($data["keeper_id"]) ? $data['keeper_id'] : '0' }}"
                                        name="keeper_id"
                                        class="form-control form-control-sm"
                                    >
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td>@lang("model.Key.room_id")</td>
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
                key_name: {
                    required: true
                },
            };

            const messages = {
                key_name: {
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
