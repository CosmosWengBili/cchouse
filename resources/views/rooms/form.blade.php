@extends('layouts.app')

@section('content')
    @include('layouts.form_error')

    @php
        $buildingId = Request::get('buildingId') ?? $data['building_id'] ?? 0;
    @endphp

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
                                    <td>@lang("model.Room.building_code")</td>
                                    <td>
                                        <select
                                            data-toggle="selectize"
                                            data-table="buildings"
                                            data-text="building_code"
                                            data-text="building_id"
                                            data-selected="{{ $buildingId ?? 0 }}"
                                            name="building_id"
                                            class="form-control form-control-sm"
                                        >
                                        </select>
                                    </td>
                                    <td>@lang("model.Room.room_code")</td>
                                    <td>
                                        <input
                                            class="form-control form-control-sm"
                                            type="text"
                                            name="room_code"
                                            value="{{ $data['room_code'] ?? '' }}"
                                            readonly
                                        />
                                    </td>
                                </tr>
                                <tr>
                                    <td>@lang("model.Room.virtual_account")</td>
                                    <td>
                                        <input
                                            class="form-control form-control-sm"
                                            type="text"
                                            name="virtual_account"
                                            value="{{ $data['virtual_account'] ?? '' }}"
                                        />
                                    </td>
                                    <td>@lang("model.Room.room_layout")</td>
                                    <td>
                                        <input
                                            class="form-control form-control-sm"
                                            type="text"
                                            name="room_layout"
                                            value="{{ $data['room_layout'] ?? '' }}"
                                        />
                                    </td>
                                </tr>
                                <tr>
                                    <td>@lang("model.Room.room_status")</td>
                                    <td>
                                        <select
                                            class="form-control form-control-sm"
                                            name="room_status"
                                            value="{{ $data['room_status'] ?? '' }}"
                                        />
                                            @foreach(config('enums.rooms.room_status') as $value)
                                                <option value="{{$value}}">{{$value}}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>@lang("model.Room.room_number")</td>
                                    <td>
                                        <input
                                            class="form-control form-control-sm"
                                            type="text"
                                            name="room_number"
                                            value="{{ $data['room_number'] ?? '' }}"
                                        />
                                    </td>
                                </tr>
                                <tr>
                                    <td>@lang("model.Room.living_room_count")</td>
                                    <td>
                                        <input
                                            class="form-control form-control-sm"
                                            type="number"
                                            name="living_room_count"
                                            value="{{ $data['living_room_count'] ?? '' }}"
                                        />
                                    </td>
                                    <td>@lang("model.Room.room_count")</td>
                                    <td>
                                        <input
                                            class="form-control form-control-sm"
                                            type="number"
                                            name="room_count"
                                            value="{{ $data['room_count'] ?? '' }}"
                                        />
                                    </td>
                                </tr>
                                <tr>
                                    <td>@lang("model.Room.bathroom_count")</td>
                                    <td>
                                        <input
                                            class="form-control form-control-sm"
                                            type="number"
                                            name="bathroom_count"
                                            value="{{ $data['bathroom_count'] ?? '' }}"
                                        />
                                    </td>
                                    <td>@lang("model.Room.parking_count")</td>
                                    <td>
                                        <input
                                            class="form-control form-control-sm"
                                            type="number"
                                            name="parking_count"
                                            value="{{ $data['parking_count'] ?? '' }}"
                                        />
                                    </td>
                                </tr>
                                <tr>
                                    <td>@lang("model.Room.rent_reserve_price")</td>
                                    <td>
                                        <input
                                            class="form-control form-control-sm"
                                            type="number"
                                            name="rent_reserve_price"
                                            value="{{ $data['rent_reserve_price'] ?? '' }}"
                                        />
                                    </td>
                                </tr>
                                <tr>
                                    <td>@lang("model.Room.rent_actual")</td>
                                    <td>
                                        <input
                                            class="form-control form-control-sm"
                                            type="number"
                                            name="rent_actual"
                                            value="{{ $data['rent_actual'] ?? '' }}"
                                        />
                                    </td>
                                    <td>@lang("model.Room.internet_form")</td>
                                    <td>
                                        <input
                                            class="form-control form-control-sm"
                                            type="text"
                                            name="internet_form"
                                            value="{{ $data['internet_form'] ?? '' }}"
                                        />
                                    </td>
                                </tr>
                                <tr>
                                    <td>@lang("model.Room.management_fee_mode")</td>
                                    <td>
                                        <select
                                            class="form-control form-control-sm"
                                            name="management_fee_mode"
                                            value="{{ $data['management_fee_mode'] ?? '' }}"
                                        />
                                            @foreach(config('enums.rooms.management_fee_mode') as $value)
                                                <option value="{{$value}}">{{$value}}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>@lang("model.Room.management_fee")</td>
                                    <td>
                                        <input
                                            class="form-control form-control-sm"
                                            type="number"
                                            step="0.01"
                                            name="management_fee"
                                            value="{{ $data['management_fee'] ?? '' }}"
                                            placeholder="$"
                                        />
                                    </td>
                                </tr>
                                <tr>
                                    <td>@lang("model.Room.wifi_account")</td>
                                    <td>
                                        <input
                                            class="form-control form-control-sm"
                                            type="text"
                                            name="wifi_account"
                                            value="{{ $data['wifi_account'] ?? '' }}"
                                        />
                                    </td>
                                    <td>@lang("model.Room.wifi_password")</td>
                                    <td>
                                        <input
                                            class="form-control form-control-sm"
                                            type="text"
                                            name="wifi_password"
                                            value="{{ $data['wifi_password'] ?? '' }}"
                                        />
                                    </td>
                                </tr>
                                <tr>
                                    <td>@lang("model.Room.has_digital_tv")</td>
                                    <td>

                                        <input type="hidden" value="0" name="has_digital_tv"/>
                                        <input
                                            type="checkbox"
                                            name="has_digital_tv"
                                            value="1"
                                            {{ isset($data["has_digital_tv"]) ? ($data['has_digital_tv'] ? 'checked' : '') : '' }}
                                        />
                                    </td>
                                    <td>@lang("model.Room.comment")</td>
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

                        <h3 class="mt-3">照片</h3>
                        @include('documents.inputs', ['documentType' => 'picture', 'documents' => $data['pictures']])

                        <h3 class="mt-3">附屬設備</h3>
                        @include('rooms.appliance', ['appliances' => $data['appliances']])

                        @if (isset( $data['room_layout'] ) && $data['room_layout'] =='公區')
                            <h3 class="mt-3">清潔紀錄</h3>
                            @include('rooms.maintenance', ['maintenances' => $data['maintenances']])
                        @endif

                        <button class="mt-5 btn btn-success" type="submit">送出</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
    <script id="validation">

        var defaultManagementFeePlaceholderText = function(){
            var management_fee_mode = $('[name="management_fee_mode"]')
            var management_fee = $('[name="management_fee"]')

            if (management_fee_mode.val() == '比例') {
                management_fee.attr('placeholder','%')
            }else{
                management_fee.attr('placeholder','$')
            }
        }

        $(document).ready(function () {
            defaultManagementFeePlaceholderText()

            $('[name="management_fee_mode"]').change(function () {
                defaultManagementFeePlaceholderText()
            })

            $.validator.addMethod('validateManagementFee',function (value,element) {
                $management_fee_mode = $('[name="management_fee_mode"]').val()
                if ($management_fee_mode == '比例') {
                    return (value > 0 && value < 100) ? true : false
                }
                return true
            })

            const rules = {
                // room_code: {
                //     required: true
                // },
                virtual_account: {
                    required: true
                },
                room_number: {
                    required: true
                },
                room_layout:{
                    required: true
                },
                living_room_count: {
                    required: true,
                },
                room_count: {
                    required: true,
                },
                bathroom_count: {
                    required: true
                },
                parking_count: {
                    required: true
                },
                rent_reserve_price: {
                    required: true
                },
                rent_actual: {
                    required: true
                },
                internet_form: {
                    required: true
                },
                management_fee: {
                    required: true,
                    validateManagementFee:true
                },
                wifi_account: {
                    required: true
                },
                wifi_password: {
                    required: true
                },
            };

            const messages = {
                room_code: {
                    required: '必須輸入'
                },
                virtual_account: {
                    required: '必須輸入'
                },
                room_number: {
                    required: '必須輸入'
                },
                living_room_count: {
                    required: '必須輸入',
                },
                room_count: {
                    required: '必須輸入',
                },
                bathroom_count: {
                    required: '必須輸入'
                },
                parking_count: {
                    required: '必須輸入'
                },
                rent_reserve_price: {
                    required: '必須輸入'
                },
                rent_actual: {
                    required: '必須輸入'
                },
                internet_form: {
                    required: '必須輸入'
                },
                management_fee: {
                    required: '必須輸入',
                    validateManagementFee: '比例: [0, 99.99]'
                },
                wifi_account: {
                    required: '必須輸入'
                },
                wifi_password: {
                    required: '必須輸入'
                },
            };

            $('form').validate({
                rules: rules,
                messages: messages,
                errorElement: "em",
                submitHandler:function(form) {
                    var documents = $('[name^="documents"]');
                    if (documents.length < 5) {
                        alert('照片至少要 5 張')
                        return false
                    }
                    for (let index = 0; index < documents.length; index++) {
                        const document = documents[index];
                        if (document.value == '') {
                            alert('照片'+(index+1)+' 沒有選擇圖片')
                            return false
                        }
                    }

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
