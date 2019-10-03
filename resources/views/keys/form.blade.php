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
                                <td>@lang("model.Key.room_id")</td>
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
                                <td>@lang("model.Key.scrap_date")</td>
                                <td>
                                    <input
                                        class="form-control form-control-sm"
                                        type="date"
                                        name="scrap_date"
                                        value="{{ isset($data["scrap_date"]) ? $data['scrap_date'] : '' }}"
                                    />
                                </td>
                                <td>@lang("model.Key.comment")</td>
                                <td>
                                    <input
                                        class="form-control form-control-sm"
                                        type="text"
                                        name="comment"
                                        value="{{ isset($data["scrap_date"]) ? $data['scrap_date'] : '' }}"
                                        placeholder="非必填"
                                    />
                                </td>
                            </tr>
                            @if (request()->route()->getName() === 'keys.edit')
                                <tr>
                                    <td>@lang("model.Key.is_scraped")</td>
                                    <td>
                                        <input type="hidden" value="0" name="is_scraped"/>
                                        <input
                                            type="checkbox"
                                            name="is_scraped"
                                            value="1"
                                            {{ isset($data["is_scraped"]) ? ($data['is_scraped'] ? 'checked' : '') : '' }}
                                        />
                                    </td>
                                </tr>
                            @endif
                            </tbody>
                        </table>

                        <h3 class="mt-3">鑰匙</h3>
                        @include('documents.inputs', ['documentType' => 'key_file', 'documents' => $data['key_files']])

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
