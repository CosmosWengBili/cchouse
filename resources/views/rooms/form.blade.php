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
                    <form action="{{$action}}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method($method)

                        <h3 class="mt-3">基本資料</h3>
                        <table class="table table-bordered">
                            <tbody>
                                <tr>
                                    <td>@lang("model.Room.building_id")</td>
                                    <td>
                                        <select 
                                            data-toggle="selectize" 
                                            data-table="buildings" 
                                            data-text="id" 
                                            data-selected="{{ $data['building_id'] ?? 0 }}"
                                            name="building_id"
                                            class="form-control form-control-sm" 
                                        >
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td>@lang("model.Room.needs_decoration")</td>
                                    <td>

                                        <input type="hidden" value="0" name="needs_decoration"/>
                                        <input
                                            type="checkbox"
                                            name="needs_decoration"
                                            value="1"
                                            {{ isset($data["needs_decoration"]) ? ($data['needs_decoration'] ? 'checked' : '') : '' }}
                                        />
                                    </td>
                                </tr>
                                <tr>
                                    <td>@lang("model.Room.room_code")</td>
                                    <td>
                                        <input
                                            class="form-control form-control-sm"
                                            type="text"
                                            name="room_code"
                                            value="{{ $data['room_code'] ?? '' }}"
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
                                </tr>
                                <tr>
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
                                    <td>@lang("model.Room.room_layout")</td>
                                    <td>
                                        <select
                                            class="form-control form-control-sm"
                                            name="room_layout"
                                            value="{{ $data['room_layout'] ?? '' }}"
                                        />
                                            @foreach(config('enums.rooms.room_layout') as $value)
                                                <option value="{{$value}}">{{$value}}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td>@lang("model.Room.room_attribute")</td>
                                    <td>
                                        <input
                                            class="form-control form-control-sm"
                                            type="text"
                                            name="room_attribute"
                                            value="{{ $data['room_attribute'] ?? '' }}"
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
                                </tr>
                                <tr>
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
                                </tr>
                                <tr>
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
                                    <td>@lang("model.Room.ammeter_reading_date")</td>
                                    <td>
                                        <input
                                            class="form-control form-control-sm"
                                            type="date"
                                            name="ammeter_reading_date"
                                            value="{{ $data['ammeter_reading_date'] ?? '' }}"
                                        />
                                    </td>
                                </tr>
                                <tr>
                                    <td>@lang("model.Room.rent_list_price")</td>
                                    <td>
                                        <input
                                            class="form-control form-control-sm"
                                            type="number"
                                            name="rent_list_price"
                                            value="{{ $data['rent_list_price'] ?? '' }}"
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
                                    <td>@lang("model.Room.rent_landlord")</td>
                                    <td>
                                        <input
                                            class="form-control form-control-sm"
                                            type="number"
                                            name="rent_landlord"
                                            value="{{ $data['rent_landlord'] ?? '' }}"
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
                                </tr>
                                <tr>
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
                                </tr>
                                <tr>
                                    <td>@lang("model.Room.management_fee")</td>
                                    <td>
                                        <input
                                            class="form-control form-control-sm"
                                            type="number"
                                            step="0.01"
                                            name="management_fee"
                                            value="{{ $data['management_fee'] ?? '' }}"
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
                                </tr>
                                <tr>
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
                                </tr>
                                <tr>
                                    <td>@lang("model.Room.can_keep_pets")</td>
                                    <td>

                                        <input type="hidden" value="0" name="can_keep_pets"/>
                                        <input
                                            type="checkbox"
                                            name="can_keep_pets"
                                            value="1"
                                            {{ isset($data["can_keep_pets"]) ? ($data['can_keep_pets'] ? 'checked' : '') : '' }}
                                        />
                                    </td>
                                </tr>
                                <tr>
                                    <td>@lang("model.Room.gender_limit")</td>
                                    <td>
                                        <select
                                            class="form-control form-control-sm"
                                            name="gender_limit"
                                            value="{{ $data['gender_limit'] ?? '' }}"
                                        />
                                            @foreach(config('enums.rooms.gender_limit') as $value)
                                                <option value="{{$value}}">{{$value}}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                </tr>
                                <tr>
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

                        <button class="mt-5 btn btn-success" type="submit">送出</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
