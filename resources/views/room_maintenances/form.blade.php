@php
    $isCreate = request()->routeIs('roomMaintenances.create');
    $roomId = Request::get('roomId') ?? $data['room_id'] ?? 0;
    // var_dump($action,$method);
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
                    <form action="{{$action}}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method($method)
                        <h3 class="mt-3">基本資料</h3>
                        <table class="table table-bordered">
                            <tbody>
                            <tr>
                                <td>@lang("model.RoomMaintenance.room_id")</td>
                                <td>
                                    <select
                                        data-toggle="selectize"
                                        data-table="rooms"
                                        data-text="room_code"
                                        data-selected="{{ $roomId }}"
                                        name="room_id"
                                        class="form-control form-control-sm"
                                        disabled
                                    >
                                    </select>
                                </td>
                                <td>@lang("model.RoomMaintenance.maintained_date")</td>
                                <td>
                                    <input
                                        class="form-control form-control-sm"
                                        type="date"
                                        name="maintained_date"
                                        value="{{ $data['maintained_date'] ?? '' }}"
                                    />
                                </td>
                            </tr>
                            <tr>
                                <td>@lang("model.RoomMaintenance.maintainer")</td>
                                <td>
                                    <input
                                        class="form-control form-control-sm"
                                        type="text"
                                        name="maintainer"
                                        value="{{ $data['maintainer'] ?? '' }}"
                                    />
                                </td>
                                <td>@lang("model.RoomMaintenance.maintained_location")</td>
                                <td>
                                    <input
                                        class="form-control form-control-sm"
                                        type="text"
                                        name="maintained_location"
                                        value="{{ $data['maintainer'] ?? '' }}"
                                    />
                                </td>
                            </tr>

                            </tbody>
                        </table>
                        <h3 class="mt-3">照片</h3>
                        @include('documents.inputs', ['documentType' => 'picture', 'documents' => $data['pictures']])

                        {{-- <input class="mt-5 btn btn-success submit" type="submit" value="送出"> --}}
                        <button class="mt-5 btn btn-success submit" type="submit">送出</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script id="validation">
    $(document).ready(function () {
        const rules = {
            room_id: {
                required: true
            },
        };

        const messages = {
            room_id: {
                required: '必須輸入'
            },
        };

        $('form').validate({
            rules: rules,
            messages: messages,
            errorElement: "em",
            submitHandler:function(form){
                $('[name="room_id"]')[0].selectize.enable()

                form.submit();
            },
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
