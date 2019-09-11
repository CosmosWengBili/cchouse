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
                        <input
                            class="form-control form-control-sm"
                            type="hidden"
                            name="key_id"
                            value="{{ isset($data["key_id"]) ? $data['key_id'] : $key_id }}"
                        >
                        <h3 class="mt-3">基本資料</h3>
                        <table class="table table-bordered">
                            <tbody>
                            <tr>
                                <td>@lang("model.KeyRequest.request_date")</td>
                                <td>
                                    <input
                                        class="form-control form-control-sm"
                                        type="date"
                                        name="request_date"
                                        value="{{ isset($data["request_date"]) ? $data['request_date'] : '' }}"
                                    />
                                </td>
                            </tr>
                            <tr>
                                <td>@lang("model.KeyRequest.request_user_id")</td>
                                <td>
                                    <select
                                        data-toggle="selectize"
                                        data-table="users"
                                        data-text="name"
                                        data-value="id"
                                        data-selected="{{ isset($data["request_user_id"]) ? $data['request_user_id'] : \Auth::user()->id }}"
                                        name="request_user_id"
                                        class="form-control form-control-sm"
                                    >
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td>@lang("model.KeyRequest.status")</td>
                                <td>
                                    <select
                                        name="status"
                                        class="form-control form-control-sm"
                                        value="{{ isset($data["status"]) ? $data['status'] : 'reserved' }}"
                                    >
                                        <option value="預約中">預約中</option>
                                        <option value="使用中">使用中</option>
                                        <option value="已完成">已完成</option>
                                    </select>
                                </td>
                            </tr>
                            @if ( $keeper_id == \Auth::user()->id )
                                <tr>
                                    <td>@lang("model.KeyRequest.request_approved")</td>
                                    <td>
                                        <input type="hidden" value="0" name="request_approved"/>
                                        <input
                                            type="checkbox"
                                            name="request_approved"
                                            value="1"
                                            {{ isset($data["request_approved"]) ? ($data['request_approved'] ? 'checked' : '') : '' }}
                                        />
                                    </td>
                                </tr>
                            @endif
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
                request_date: {
                    required: true
                },
            };

            const messages = {
                request_date: {
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
