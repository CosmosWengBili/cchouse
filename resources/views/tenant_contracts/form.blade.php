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
                                    <td>@lang("model.TenantContract.room_id")</td>
                                    <td>
                                        <select 
                                            data-toggle="selectize" 
                                            data-table="user" 
                                            data-text="id" 
                                            data-selected="{{ $data['room_id'] ?? 0 }}"
                                            name="room_id"
                                            class="form-control form-control-sm" 
                                        >
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td>@lang("model.TenantContract.tenant_id")</td>
                                    <td>
                                        <select 
                                            data-toggle="selectize" 
                                            data-table="user" 
                                            data-text="id" 
                                            data-selected="{{ $data['tenant_id'] ?? 0 }}"
                                            name="tenant_id"
                                            class="form-control form-control-sm" 
                                        >
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td>@lang("model.TenantContract.contract_serial_number")</td>
                                    <td>
                                        <input
                                            class="form-control form-control-sm"
                                            type="text"
                                            name="contract_serial_number"
                                            value="{{ $data['contract_serial_number'] ?? '' }}"
                                        />
                                    </td>
                                </tr>
                                <tr>
                                    <td>@lang("model.TenantContract.set_other_rights")</td>
                                    <td>

                                        <input type="hidden" value="0" name="set_other_rights"/>
                                        <input
                                            type="checkbox"
                                            name="set_other_rights"
                                            value="1"
                                            {{ isset($data["set_other_rights"]) ? ($data['set_other_rights'] ? 'checked' : '') : '' }}
                                        />
                                    </td>
                                </tr>
                                <tr>
                                    <td>@lang("model.TenantContract.other_rights")</td>
                                    <td>
                                        <input
                                            class="form-control form-control-sm"
                                            type="text"
                                            name="other_rights"
                                            value="{{ $data['other_rights'] ?? '' }}"
                                        />
                                    </td>
                                </tr>
                                <tr>
                                    <td>@lang("model.TenantContract.sealed_registered")</td>
                                    <td>

                                        <input type="hidden" value="0" name="sealed_registered"/>
                                        <input
                                            type="checkbox"
                                            name="sealed_registered"
                                            value="1"
                                            {{ isset($data["sealed_registered"]) ? ($data['sealed_registered'] ? 'checked' : '') : '' }}
                                        />
                                    </td>
                                </tr>
                                <tr>
                                    <td>@lang("model.TenantContract.car_parking_floor")</td>
                                    <td>
                                        <input
                                            class="form-control form-control-sm"
                                            type="text"
                                            name="car_parking_floor"
                                            value="{{ $data['car_parking_floor'] ?? '' }}"
                                        />
                                    </td>
                                </tr>
                                <tr>
                                    <td>@lang("model.TenantContract.car_parking_type")</td>
                                    <td>
                                        <select
                                            class="form-control form-control-sm"
                                            name="car_parking_type"
                                            value="{{ $data['car_parking_type'] ?? '' }}"
                                        />
                                            @foreach(config('enums.tenant_contract.car_parking_type') as $value)
                                                <option value="{{$value}}">{{$value}}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td>@lang("model.TenantContract.car_parking_space_number")</td>
                                    <td>
                                        <input
                                            class="form-control form-control-sm"
                                            type="text"
                                            name="car_parking_space_number"
                                            value="{{ $data['car_parking_space_number'] ?? '' }}"
                                        />
                                    </td>
                                </tr>
                                <tr>
                                    <td>@lang("model.TenantContract.motorcycle_parking_floor")</td>
                                    <td>
                                        <input
                                            class="form-control form-control-sm"
                                            type="text"
                                            name="motorcycle_parking_floor"
                                            value="{{ $data['motorcycle_parking_floor'] ?? '' }}"
                                        />
                                    </td>
                                </tr>
                                <tr>
                                    <td>@lang("model.TenantContract.motorcycle_parking_space_number")</td>
                                    <td>
                                        <input
                                            class="form-control form-control-sm"
                                            type="text"
                                            name="motorcycle_parking_space_number"
                                            value="{{ $data['motorcycle_parking_space_number'] ?? '' }}"
                                        />
                                    </td>
                                </tr>
                                <tr>
                                    <td>@lang("model.TenantContract.motorcycle_parking_count")</td>
                                    <td>
                                        <input
                                            class="form-control form-control-sm"
                                            type="number"
                                            name="motorcycle_parking_count"
                                            value="{{ $data['motorcycle_parking_count'] ?? '' }}"
                                        />
                                    </td>
                                </tr>
                                <tr>
                                    <td>@lang("model.TenantContract.effective")</td>
                                    <td>

                                        <input type="hidden" value="0" name="effective"/>
                                        <input
                                            type="checkbox"
                                            name="effective"
                                            value="1"
                                            {{ isset($data["effective"]) ? ($data['effective'] ? 'checked' : '') : '' }}
                                        />
                                    </td>
                                </tr>
                                <tr>
                                    <td>@lang("model.TenantContract.contract_start")</td>
                                    <td>
                                        <input
                                            class="form-control form-control-sm"
                                            type="date"
                                            name="contract_start"
                                            value="{{ $data['contract_start'] ?? '' }}"
                                        />
                                    </td>
                                </tr>
                                <tr>
                                    <td>@lang("model.TenantContract.contract_end")</td>
                                    <td>
                                        <input
                                            class="form-control form-control-sm"
                                            type="date"
                                            name="contract_end"
                                            value="{{ $data['contract_end'] ?? '' }}"
                                        />
                                    </td>
                                </tr>
                                <tr>
                                    <td>@lang("model.TenantContract.rent")</td>
                                    <td>
                                        <input
                                            class="form-control form-control-sm"
                                            type="number"
                                            name="rent"
                                            value="{{ $data['rent'] ?? '' }}"
                                        />
                                    </td>
                                </tr>
                                <tr>
                                    <td>@lang("model.TenantContract.rent_pay_day")</td>
                                    <td>
                                        <input
                                            class="form-control form-control-sm"
                                            type="number"
                                            name="rent_pay_day"
                                            value="{{ $data['rent_pay_day'] ?? '' }}"
                                        />
                                    </td>
                                </tr>
                                <tr>
                                    <td>@lang("model.TenantContract.deposit")</td>
                                    <td>
                                        <input
                                            class="form-control form-control-sm"
                                            type="number"
                                            name="deposit"
                                            value="{{ $data['deposit'] ?? '' }}"
                                        />
                                    </td>
                                </tr>
                                <tr>
                                    <td>@lang("model.TenantContract.deposit_paid")</td>
                                    <td>
                                        <input
                                            class="form-control form-control-sm"
                                            type="number"
                                            name="deposit_paid"
                                            value="{{ $data['deposit_paid'] ?? '' }}"
                                        />
                                    </td>
                                </tr>
                                <tr>
                                    <td>@lang("model.TenantContract.electricity_payment_method")</td>
                                    <td>
                                        <select
                                            class="form-control form-control-sm"
                                            name="electricity_payment_method"
                                            value="{{ $data['electricity_payment_method'] ?? '' }}"
                                        />
                                            @foreach(config('enums.tenant_contract.electricity_payment_method') as $value)
                                                <option value="{{$value}}">{{$value}}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td>@lang("model.TenantContract.electricity_calculate_method")</td>
                                    <td>
                                        <select
                                            class="form-control form-control-sm"
                                            name="electricity_calculate_method"
                                            value="{{ $data['electricity_calculate_method'] ?? '' }}"
                                        />
                                            @foreach(config('enums.tenant_contract.electricity_calculate_method') as $value)
                                                <option value="{{$value}}">{{$value}}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td>@lang("model.TenantContract.electricity_price_per_degree")</td>
                                    <td>
                                        <input
                                            class="form-control form-control-sm"
                                            type="number"
                                            step="0.01"
                                            name="electricity_price_per_degree"
                                            value="{{ $data['electricity_price_per_degree'] ?? '' }}"
                                        />
                                    </td>
                                </tr>
                                <tr>
                                    <td>@lang("model.TenantContract.electricity_price_per_degree_summer")</td>
                                    <td>
                                        <input
                                            class="form-control form-control-sm"
                                            type="number"
                                            step="0.01"
                                            name="electricity_price_per_degree_summer"
                                            value="{{ $data['electricity_price_per_degree_summer'] ?? '' }}"
                                        />
                                    </td>
                                </tr>
                                <tr>
                                    <td>@lang("model.TenantContract.110v_start_degree")</td>
                                    <td>
                                        <input
                                            class="form-control form-control-sm"
                                            type="number"
                                            name="110v_start_degree"
                                            value="{{ $data['110v_start_degree'] ?? '' }}"
                                        />
                                    </td>
                                </tr>
                                <tr>
                                    <td>@lang("model.TenantContract.220v_start_degree")</td>
                                    <td>
                                        <input
                                            class="form-control form-control-sm"
                                            type="number"
                                            name="220v_start_degree"
                                            value="{{ $data['220v_start_degree'] ?? '' }}"
                                        />
                                    </td>
                                </tr>
                                <tr>
                                    <td>@lang("model.TenantContract.110v_end_degree")</td>
                                    <td>
                                        <input
                                            class="form-control form-control-sm"
                                            type="number"
                                            name="110v_end_degree"
                                            value="{{ $data['110v_end_degree'] ?? '' }}"
                                        />
                                    </td>
                                </tr>
                                <tr>
                                    <td>@lang("model.TenantContract.220v_end_degree")</td>
                                    <td>
                                        <input
                                            class="form-control form-control-sm"
                                            type="number"
                                            name="220v_end_degree"
                                            value="{{ $data['220v_end_degree'] ?? '' }}"
                                        />
                                    </td>
                                </tr>
                                <tr>
                                    <td>@lang("model.TenantContract.invoice_collection_method")</td>
                                    <td>
                                        <select
                                            class="form-control form-control-sm"
                                            name="invoice_collection_method"
                                            value="{{ $data['invoice_collection_method'] ?? '' }}"
                                        />
                                            @foreach(config('enums.tenant_contract.invoice_collection_method') as $value)
                                                <option value="{{$value}}">{{$value}}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td>@lang("model.TenantContract.invoice_collection_number")</td>
                                    <td>
                                        <input
                                            class="form-control form-control-sm"
                                            type="text"
                                            name="invoice_collection_number"
                                            value="{{ $data['invoice_collection_number'] ?? '' }}"
                                        />
                                    </td>
                                </tr>
                                <tr>
                                    <td>@lang("model.TenantContract.commissioner_id")</td>
                                    <td>
                                        <select 
                                            data-toggle="selectize" 
                                            data-table="user" 
                                            data-text="id" 
                                            data-selected="{{ $data['commissioner_id'] ?? 0 }}"
                                            name="commissioner_id"
                                            class="form-control form-control-sm" 
                                        >
                                        </select>
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                        <div>
                            {{-- generate the following block to have more payments --}}
                            {{-- important: specify the id increment using js or whatever way you like in name attribute --}}
                            {{-- payments[0][subject], payments[1][subject], payments[2][subject] ... --}}
                            <div class="each kind of payment">
                                <span>科目</span>
                                <select
                                    class="form-control form-control-sm"
                                    name="payments[0][subject]"
                                    value=""
                                />
                                    @foreach(config('enums.tenant_payments.subject') as $value)
                                        <option value="{{$value}}">{{$value}}</option>
                                    @endforeach
                                </select>

                                <span>頻率</span>
                                <select
                                    class="form-control form-control-sm"
                                    name="payments[0][period]"
                                    value=""
                                />
                                    @foreach(config('enums.tenant_payments.period') as $value)
                                        <option value="{{$value}}">{{$value}}</option>
                                    @endforeach
                                </select>
                                
                                <span>費用</span>
                                <input
                                    class="form-control form-control-sm"
                                    type="number"
                                    name="payments[0][amount]"
                                    value="{{ $data['invoice_collection_number'] ?? '' }}"
                                />

                                <span>收取</span>
                                <select
                                    class="form-control form-control-sm"
                                    name="payments[0][collected_by]"
                                    value=""
                                />
                                    @foreach(config('enums.tenant_payments.collected_by') as $value)
                                        <option value="{{$value}}">{{$value}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <button class="mt-5 btn btn-success" type="submit">送出</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
