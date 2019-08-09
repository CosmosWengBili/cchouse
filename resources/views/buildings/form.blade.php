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
                                    <td>@lang("model.Building.title")</td>
                                    <td>
                                        <input
                                            class="form-control form-control-sm"
                                            type="text"
                                            name="title"
                                            value="{{ $data['title'] ?? '' }}"
                                        />
                                    </td>
                                </tr>
                                <tr>
                                    <td>@lang("model.Building.city")</td>
                                    <td>
                                        <input
                                            class="form-control form-control-sm"
                                            type="text"
                                            name="city"
                                            value="{{ $data['city'] ?? '' }}"
                                        />
                                    </td>
                                </tr>
                                <tr>
                                    <td>@lang("model.Building.district")</td>
                                    <td>
                                        <input
                                            class="form-control form-control-sm"
                                            type="text"
                                            name="district"
                                            value="{{ $data['district'] ?? '' }}"
                                        />
                                    </td>
                                </tr>
                                <tr>
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
                                    <td>@lang("model.Building.tax_number")</td>
                                    <td>
                                        <input
                                            class="form-control form-control-sm"
                                            type="text"
                                            name="tax_number"
                                            value="{{ $data['tax_number'] ?? '' }}"
                                        />
                                    </td>
                                </tr>
                                <tr>
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
                                </tr>
                                <tr>
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
                                </tr>
                                <tr>
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
                                </tr>
                                <tr>
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
                                </tr>
                                <tr>
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
                                </tr>
                                <tr>
                                    <td>@lang("model.Building.electricity_payment_method")</td>
                                    <td>
                                        <select
                                            class="form-control form-control-sm"
                                            name="electricity_payment_method"
                                            value="{{ $data['electricity_payment_method'] ?? '' }}"
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
                                </tr>
                                <tr>
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
                                </tr>
                                <tr>
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
                                </tr>
                                <tr>
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
                                </tr>
                                <tr>
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
                                </tr>
                                <tr>
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
                                </tr>
                                <tr>
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
                                    <td>@lang("model.Building.accounting_group")</td>
                                    <td>
                                        <input
                                            class="form-control form-control-sm"
                                            type="text"
                                            name="accounting_group"
                                            value="{{ $data['accounting_group'] ?? '' }}"
                                        />
                                    </td>
                                </tr>
                                <tr>
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
                                    <td>@lang("model.Building.commissioner_id")</td>
                                    <td>
                                        <select 
                                            data-toggle="selectize" 
                                            data-table="user" 
                                            data-text="name" 
                                            data-selected="{{ $data['commissioner_id'] ?? 0 }}"
                                            name="commissioner_id"
                                            class="form-control form-control-sm" 
                                        >
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td>@lang("model.Building.administrator_id")</td>
                                    <td>
                                        <select 
                                            data-toggle="selectize" 
                                            data-table="user" 
                                            data-text="name" 
                                            data-selected="{{ $data['administrator_id'] ?? 0 }}"
                                            name="administrator_id"
                                            class="form-control form-control-sm" 
                                        >
                                        </select>
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
@endsection
