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
                                    <td>@lang("model.Building.title")</td>
                                    <td>
                                        <input
                                            class="form-control form-control-sm"
                                            type="text"
                                            name="title"
                                            value="{{ $data['title'] ?? '' }}"
                                        />
                                    </td>
                                    <td>@lang("model.Building.building_code")</td>
                                    <td>
                                        <input
                                            class="form-control form-control-sm"
                                            type="text"
                                            name="building_code"
                                            value="{{ $data['building_code'] ?? '' }}"
                                        />
                                    </td>
                                </tr>
                                <tr>
                                    <td>@lang("model.Building.group")</td>
                                    <td>
                                        <input
                                            class="form-control form-control-sm"
                                            type="text"
                                            name="group"
                                            value="{{ $data['group'] ?? '' }}"
                                        />
                                    </td>
                                    <td>@lang("model.Building.city")</td>
                                    <td>
                                        <select
                                            class="form-control form-control-sm"
                                            name="city"
                                            value="{{ $data['city'] ?? '' }}"
                                        />
                                            @foreach(config('enums.cities') as $key => $value)
                                                <option value="{{$key}}">{{$key}}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td>@lang("model.Building.district")</td>
                                    <td>
                                        <select
                                            class="form-control form-control-sm"
                                            name="district"
                                            value="{{ $data['district'] ?? '' }}"
                                        />
                                            @if(isset($data['city']) && $data['city'])
                                                @foreach(config('enums.cities')[$data['city']] as $value)
                                                    <option value="{{$value}}">{{$value}}</option>
                                                @endforeach
                                            @else
                                                @foreach(config('enums.cities')['臺北市'] as $value)
                                                    <option value="{{$value}}">{{$value}}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </td>
                                    <td>@lang("model.Building.address")</td>
                                    <td>
                                        <input
                                            class="form-control form-control-sm"
                                            type="text"
                                            name="address"
                                            value="{{ $data['address'] ?? '' }}"
                                        />
                                    </td>
                                </tr>
                                <tr>
                                    <td>@lang("model.Building.is_squatter")</td>
                                    <td>
                                        <input type="hidden" value="0" name="is_squatter"/>
                                        <input
                                            type="checkbox"
                                            name="is_squatter"
                                            value="1"
                                            {{ isset($data["is_squatter"]) ? ($data['is_squatter'] ? 'checked' : '') : '' }}
                                        />
                                    </td>
                                    <td>@lang("model.Building.squatter_status")</td>
                                    <td>
                                        <input
                                            class="form-control form-control-sm"
                                            type="text"
                                            name="squatter_status"
                                            value="{{ $data['squatter_status'] ?? '' }}"
                                        />
                                    </td>
                                </tr>
                                <tr>
                                    <td>@lang("model.Building.decoration_needed")</td>
                                    <td>
                                        <input type="hidden" value="0" name="decoration_needed"/>
                                        <input
                                            type="checkbox"
                                            name="decoration_needed"
                                            value="1"
                                            {{ isset($data["decoration_needed"]) ? ($data['decoration_needed'] ? 'checked' : '') : '' }}
                                        />
                                    </td>
                                    <td>@lang("model.Building.decoration_price")</td>
                                    <td>
                                        <input
                                            class="form-control form-control-sm"
                                            type="number"
                                            name="decoration_price"
                                            value="{{ $data['decoration_price'] ?? '' }}"
                                        />
                                    </td>
                                </tr>
                                <tr>
                                    <td>@lang("model.Building.tax_number")</td>
                                    <td>
                                        <input
                                            class="form-control form-control-sm"
                                            type="text"
                                            name="tax_number"
                                            value="{{ $data['tax_number'] ?? '' }}"
                                        />
                                    </td>
                                    <td>@lang("model.Building.building_type")</td>
                                    <td>
                                        <select
                                            class="form-control form-control-sm"
                                            name="building_type"
                                            value="{{ $data['building_type'] ?? '' }}"
                                        />
                                            @foreach(config('enums.buildings.building_type') as $value)
                                                <option value="{{$value}}">{{$value}}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td>@lang("model.Building.floor")</td>
                                    <td>
                                        <input
                                            class="form-control form-control-sm"
                                            type="number"
                                            name="floor"
                                            value="{{ $data['floor'] ?? '' }}"
                                        />
                                    </td>
                                    <td>@lang("model.Building.administrative_number")</td>
                                    <td>
                                        <input
                                            class="form-control form-control-sm"
                                            type="text"
                                            name="administrative_number"
                                            value="{{ $data['administrative_number'] ?? '' }}"
                                        />
                                    </td>
                                </tr>
                                <tr>
                                    <td>@lang("model.Building.legal_usage")</td>
                                    <td>
                                        <select
                                            class="form-control form-control-sm"
                                            name="legal_usage"
                                            value="{{ $data['legal_usage'] ?? '' }}"
                                        />
                                            @foreach(config('enums.buildings.legal_usage') as $value)
                                                <option value="{{$value}}">{{$value}}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>@lang("model.Building.land_use")</td>
                                    <td>
                                        <input
                                            class="form-control form-control-sm"
                                            type="text"
                                            name="land_use"
                                            value="{{ $data['land_use'] ?? '' }}"
                                        />
                                    </td>
                                </tr>
                                <tr>
                                    <td>@lang("model.Building.has_elevator")</td>
                                    <td>

                                        <input type="hidden" value="0" name="has_elevator"/>
                                        <input
                                            type="checkbox"
                                            name="has_elevator"
                                            value="1"
                                            {{ isset($data["has_elevator"]) ? ($data['has_elevator'] ? 'checked' : '') : '' }}
                                        />
                                    </td>
                                    <td>@lang("model.Building.security_guard")</td>
                                    <td>
                                        <input
                                            class="form-control form-control-sm"
                                            type="text"
                                            name="security_guard"
                                            value="{{ $data['security_guard'] ?? '' }}"
                                        />
                                    </td>
                                </tr>
                                <tr>
                                    <td>@lang("model.Building.management_count")</td>
                                    <td>
                                        <input
                                            class="form-control form-control-sm"
                                            type="text"
                                            name="management_count"
                                            value="{{ $data['management_count'] ?? '' }}"
                                        />
                                    </td>
                                    <td>@lang("model.Building.first_floor_door_opening")</td>
                                    <td>
                                        <input
                                            class="form-control form-control-sm"
                                            type="text"
                                            name="first_floor_door_opening"
                                            value="{{ $data['first_floor_door_opening'] ?? '' }}"
                                        />
                                    </td>
                                </tr>
                                <tr>
                                    <td>@lang("model.Building.public_area_door_opening")</td>
                                    <td>
                                        <input
                                            class="form-control form-control-sm"
                                            type="text"
                                            name="public_area_door_opening"
                                            value="{{ $data['public_area_door_opening'] ?? '' }}"
                                        />
                                    </td>
                                    <td>@lang("model.Building.room_door_opening")</td>
                                    <td>
                                        <input
                                            class="form-control form-control-sm"
                                            type="text"
                                            name="room_door_opening"
                                            value="{{ $data['room_door_opening'] ?? '' }}"
                                        />
                                    </td>
                                </tr>
                                <tr>
                                    <td>@lang("model.Building.main_ammeter_location")</td>
                                    <td>
                                        <input
                                            class="form-control form-control-sm"
                                            type="text"
                                            name="main_ammeter_location"
                                            value="{{ $data['main_ammeter_location'] ?? '' }}"
                                        />
                                    </td>
                                    <td>@lang("model.Building.ammeter_serial_number_1")</td>
                                    <td>
                                        <input
                                            class="form-control form-control-sm"
                                            type="text"
                                            name="ammeter_serial_number_1"
                                            value="{{ $data['ammeter_serial_number_1'] ?? '' }}"
                                        />
                                    </td>
                                </tr>
                                <tr>
                                    <td>@lang("model.Building.shared_electricity")</td>
                                    <td>
                                        <input
                                            class="form-control form-control-sm"
                                            type="text"
                                            name="shared_electricity"
                                            value="{{ $data['shared_electricity'] ?? '' }}"
                                        />
                                    </td>
                                    <td>@lang("model.Building.taiwan_electricity_payment_method")</td>
                                    <td>
                                        <select
                                            class="form-control form-control-sm"
                                            name="taiwan_electricity_payment_method"
                                            value="{{ $data['taiwan_electricity_payment_method'] ?? '' }}"
                                        />
                                            @foreach(config('enums.buildings.payment_methods') as $value)
                                                <option value="{{$value}}">{{$value}}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td>@lang("model.Building.private_ammeter_location")</td>
                                    <td>
                                        <input
                                            class="form-control form-control-sm"
                                            type="text"
                                            name="private_ammeter_location"
                                            value="{{ $data['private_ammeter_location'] ?? '' }}"
                                        />
                                    </td>
                                    <td>@lang("model.Building.water_meter_location")</td>
                                    <td>
                                        <input
                                            class="form-control form-control-sm"
                                            type="text"
                                            name="water_meter_location"
                                            value="{{ $data['water_meter_location'] ?? '' }}"
                                        />
                                    </td>
                                </tr>
                                <tr>
                                    <td>@lang("model.Building.water_meter_serial_number")</td>
                                    <td>
                                        <input
                                            class="form-control form-control-sm"
                                            type="text"
                                            name="water_meter_serial_number"
                                            value="{{ $data['water_meter_serial_number'] ?? '' }}"
                                        />
                                    </td>
                                    <td>@lang("model.Building.water_payment_method")</td>
                                    <td>
                                        <select
                                            class="form-control form-control-sm"
                                            name="water_payment_method"
                                            value="{{ $data['water_payment_method'] ?? '' }}"
                                        />
                                            @foreach(config('enums.buildings.payment_methods') as $value)
                                                <option value="{{$value}}">{{$value}}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td>@lang("model.Building.water_meter_reading_date")</td>
                                    <td>
                                        <input
                                            class="form-control form-control-sm"
                                            type="date"
                                            name="water_meter_reading_date"
                                            value="{{ $data['water_meter_reading_date'] ?? '' }}"
                                        />
                                    </td>
                                    <td>@lang("model.Building.gas_meter_location")</td>
                                    <td>
                                        <input
                                            class="form-control form-control-sm"
                                            type="text"
                                            name="gas_meter_location"
                                            value="{{ $data['gas_meter_location'] ?? '' }}"
                                        />
                                    </td>
                                </tr>
                                <tr>
                                    <td>@lang("model.Building.garbage_collection_location")</td>
                                    <td>
                                        <input
                                            class="form-control form-control-sm"
                                            type="text"
                                            name="garbage_collection_location"
                                            value="{{ $data['garbage_collection_location'] ?? '' }}"
                                        />
                                    </td>
                                    <td>@lang("model.Building.garbage_collection_time")</td>
                                    <td>
                                        <input
                                            class="form-control form-control-sm"
                                            type="text"
                                            name="garbage_collection_time"
                                            value="{{ $data['garbage_collection_time'] ?? '' }}"
                                        />
                                    </td>
                                </tr>
                                <tr>
                                    <td>@lang("model.Building.management_fee_payment_method")</td>
                                    <td>
                                        <select
                                            class="form-control form-control-sm"
                                            name="management_fee_payment_method"
                                            value="{{ $data['management_fee_payment_method'] ?? '' }}"
                                        />
                                            @foreach(config('enums.buildings.payment_methods') as $value)
                                                <option value="{{$value}}">{{$value}}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>@lang("model.Building.management_fee_contact")</td>
                                    <td>
                                        <input
                                            class="form-control form-control-sm"
                                            type="text"
                                            name="management_fee_contact"
                                            value="{{ $data['management_fee_contact'] ?? '' }}"
                                        />
                                    </td>
                                </tr>
                                <tr>
                                    <td>@lang("model.Building.management_fee_contact_phone")</td>
                                    <td>
                                        <input
                                            class="form-control form-control-sm"
                                            type="text"
                                            name="management_fee_contact_phone"
                                            value="{{ $data['management_fee_contact_phone'] ?? '' }}"
                                        />
                                    </td>
                                    <td>@lang("model.Building.distribution_method")</td>
                                    <td>
                                        <input
                                            class="form-control form-control-sm"
                                            type="text"
                                            name="distribution_method"
                                            value="{{ $data['distribution_method'] ?? '' }}"
                                        />
                                    </td>
                                </tr>
                                <tr>
                                    <td>@lang("model.Building.commission_group")</td>
                                    <td>
                                        <input
                                            class="form-control form-control-sm"
                                            type="text"
                                            name="commission_group"
                                            value="{{ $data['commission_group'] ?? '' }}"
                                        />
                                    </td>
                                    <td>@lang("model.Building.commissioner_id")</td>
                                    <td>
                                        <select
                                            data-toggle="selectize"
                                            data-table="users"
                                            data-text="name"
                                            data-selected="{{ $data['commissioner_id'] ?? 0 }}"
                                            name="commissioner_id"
                                            class="form-control form-control-sm"
                                        >
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td>@lang("model.Building.accounting_group")</td>
                                    <td>
                                        <input
                                            class="form-control form-control-sm"
                                            type="text"
                                            name="accounting_group"
                                            value="{{ $data['accounting_group'] ?? '' }}"
                                        />
                                    </td>

                                    <td>@lang("model.Building.administrator_id")</td>
                                    <td>
                                        <select
                                            data-toggle="selectize"
                                            data-table="users"
                                            data-text="name"
                                            data-selected="{{ $data['administrator_id'] ?? 0 }}"
                                            name="administrator_id"
                                            class="form-control form-control-sm"
                                        >
                                        </select>
                                    </td>

                                </tr>
                                <tr>
                                    <td>@lang("model.Building.electricity_payment_method")</td>
                                    <td>
                                        <select
                                            class="form-control form-control-sm"
                                            name="electricity_payment_method"
                                            value="{{ $data['electricity_payment_method'] ?? '' }}"
                                        >
                                            @foreach(config('enums.buildings.electricity_payment_method') as $value)
                                                <option value="{{$value}}">{{$value}}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>@lang("model.Building.rental_receipt")</td>
                                    <td>
                                        <input
                                            class="form-control form-control-sm"
                                            type="text"
                                            name="rental_receipt"
                                            value="{{ $data['rental_receipt'] ?? '' }}"
                                        />
                                    </td>
                                </tr>
                                <tr>
                                    <td>@lang("model.Building.comment")</td>
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
<script>
    const addressObject = {}
    var array = []
    @foreach(config('enums.cities') as $city_key => $districts)
        array = []
        @foreach($districts as $index_key => $value)
        array.push('{{$value}}')
        @endforeach
        addressObject['{{$city_key}}'] = array
    @endforeach

    $('[name=city]').on('change', function(){
        let city = $(this).val()
        let districts = addressObject[city]
        $('[name=district]').html('')
        districts.forEach(function(value, idx){
            $('[name=district]').append(`<option value='${value}'>${value}</option>`)
        })
    })
</script>
<script id="validation">

    $(document).ready(function () {

        const rules = {
            title: {
                required: true
            },
            building_code:{
                required: true
            },
            city:{
                required: true
            },
            district:{
                required: true
            },
            address: {
                required: true
            },
            tax_number: {
                required: true
            },
            floor: {
                required: true
            },
            // security_guard: {
            //     required: true,
            // },
            management_count: {
                required: true,
            },
            // first_floor_door_opening: {
            //     required: true
            // },
            // public_area_door_opening: {
            //     required: true
            // },
            // room_door_opening: {
            //     required: true
            // },
            main_ammeter_location: {
                required: true
            },
            ammeter_serial_number_1: {
                required: true
            },
            // shared_electricity: {
            //     required: true
            // },
            // private_ammeter_location: {
            //     required: true
            // },
            water_meter_location: {
                required: true
            },
            water_meter_serial_number: {
                required: true
            },
            water_meter_reading_date: {
                required: true
            },
            gas_meter_location: {
                required: true
            },
            // garbage_collection_location: {
            //     required: true
            // },
            // garbage_collection_time: {
            //     required: true
            // },
            management_fee_contact: {
                required: true
            },
            management_fee_contact_phone: {
                required: true
            },
            // distribution_method: {
            //     required: true
            // },
            administrative_number: {
                required: true
            },
            // accounting_group: {
            //     required: true
            // },
            // rental_receipt: {
            //     required: true
            // },
            decoration_price:{
                required: true
            }
        };

        const messages = {
            title: {
                required: '必須輸入'
            },
            address: {
                required: '必須輸入'
            },
            tax_number: {
                required: '必須輸入'
            },
            floor: {
                required: '必須輸入'
            },
            security_guard: {
                required: '必須輸入',
            },
            management_count: {
                required: '必須輸入',
            },
            first_floor_door_opening: {
                required: '必須輸入'
            },
            public_area_door_opening: {
                required: '必須輸入'
            },
            room_door_opening: {
                required: '必須輸入'
            },
            main_ammeter_location: {
                required: '必須輸入'
            },
            ammeter_serial_number_1: {
                required: '必須輸入'
            },
            shared_electricity: {
                required: '必須輸入'
            },
            private_ammeter_location: {
                required: '必須輸入'
            },
            water_meter_location: {
                required: '必須輸入'
            },
            water_meter_serial_number: {
                required: '必須輸入'
            },
            water_meter_reading_date: {
                required: '必須輸入'
            },
            gas_meter_location: {
                required: '必須輸入'
            },
            garbage_collection_location: {
                required: '必須輸入'
            },
            garbage_collection_time: {
                required: '必須輸入'
            },
            management_fee_contact: {
                required: '必須輸入'
            },
            management_fee_contact_phone: {
                required: '必須輸入'
            },
            distribution_method: {
                required: '必須輸入'
            },
            administrative_number: {
                required: '必須輸入'
            },
            accounting_group: {
                required: '必須輸入'
            },
            rental_receipt: {
                required: '必須輸入'
            },
            decoration_price:{
                required: '必須輸入'
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
