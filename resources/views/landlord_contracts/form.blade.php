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
                                <td>@lang("model.LandlordContract.landlord_id")</td>
                                <td>
                                    <select 
                                        data-toggle="selectize" 
                                        data-table="landlord" 
                                        data-text="name" 
                                        data-value="id" 
                                        data-selected="{{ isset($data["landlord_id"]) ? $data['landlord_id'] : '0' }}"
                                        name="landlord_id"
                                        class="form-control form-control-sm" 
                                    >
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td>@lang("model.LandlordContract.building_id")</td>
                                <td>
                                    <select 
                                        data-toggle="selectize" 
                                        data-table="building" 
                                        data-text="address" 
                                        data-value="id" 
                                        data-selected="{{ isset($data["building_id"]) ? $data['building_id'] : '0' }}"
                                        name="building_id"
                                        class="form-control form-control-sm" 
                                    >
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td>@lang("model.LandlordContract.commission_type")</td>
                                <td>
                                    <select
                                        class="form-control form-control-sm"
                                        name="commission_type"
                                        value="{{ isset($data["commission_type"]) ? $data['commission_type'] : '' }}"
                                    />
                                        <option value="charter">包租</option>
                                        <option value="escrow">代管</option>
                                    </select>
                                </td>
                            </tr>

                            <tr>
                                <td>@lang("model.LandlordContract.commission_start_date")</td>
                                <td>
                                    <input
                                        class="form-control form-control-sm"
                                        type="date"
                                        name="commission_start_date"
                                        value="{{ isset($data["commission_start_date"]) ? $data['commission_start_date'] : '' }}"
                                    />
                                </td>
                            </tr>
                            <tr>
                                <td>@lang("model.LandlordContract.commission_end_date")</td>
                                <td>
                                    <input
                                        class="form-control form-control-sm"
                                        type="date"
                                        name="commission_end_date"
                                        value="{{ isset($data["commission_end_date"]) ? $data['commission_end_date'] : '' }}"
                                    />
                                </td>
                            </tr>
                            <tr>
                                <td>@lang("model.LandlordContract.warranty_start_date")</td>
                                <td>
                                    <input
                                        class="form-control form-control-sm"
                                        type="date"
                                        name="warranty_start_date"
                                        value="{{ isset($data["warranty_start_date"]) ? $data['warranty_start_date'] : '' }}"
                                    />
                                </td>
                            </tr>
                            <tr>
                                <td>@lang("model.LandlordContract.warranty_end_date")</td>
                                <td>
                                    <input
                                        class="form-control form-control-sm"
                                        type="date"
                                        name="warranty_end_date"
                                        value="{{ isset($data["warranty_end_date"]) ? $data['warranty_end_date'] : '' }}"
                                    />
                                </td>
                            </tr>
                            <tr>
                                <td>@lang("model.LandlordContract.rental_decoration_free_start_date")</td>
                                <td>
                                    <input
                                        class="form-control form-control-sm"
                                        type="date"
                                        name="rental_decoration_free_start_date"
                                        value="{{ isset($data["rental_decoration_free_start_date"]) ? $data['rental_decoration_free_start_date'] : '' }}"
                                    />
                                </td>
                            </tr>
                            <tr>
                                <td>@lang("model.LandlordContract.rental_decoration_free_end_date")</td>
                                <td>
                                    <input
                                        class="form-control form-control-sm"
                                        type="date"
                                        name="rental_decoration_free_end_date"
                                        value="{{ isset($data["rental_decoration_free_end_date"]) ? $data['rental_decoration_free_end_date'] : '' }}"
                                    />
                                </td>
                            </tr>
    
                            <tr>
                                <td>@lang("model.LandlordContract.annual_service_fee_month_count")</td>
                                <td>
                                    <input
                                        class="form-control form-control-sm"
                                        type="text"
                                        name="annual_service_fee_month_count"
                                        value="{{ isset($data["annual_service_fee_month_count"]) ? $data['annual_service_fee_month_count'] : '' }}"
                                    />
                                </td>
                            </tr>

                            <tr>
                                <td>@lang("model.LandlordContract.charter_fee")</td>
                                <td>
                                    <input
                                        class="form-control form-control-sm"
                                        type="text"
                                        name="charter_fee"
                                        value="{{ isset($data["charter_fee"]) ? $data['charter_fee'] : '' }}"
                                    />
                                </td>
                            </tr>

                            <tr>
                                <td>@lang("model.LandlordContract.taxable_charter_fee")</td>
                                <td>
                                    <input
                                        class="form-control form-control-sm"
                                        type="text"
                                        name="taxable_charter_fee"
                                        value="{{ isset($data["taxable_charter_fee"]) ? $data['taxable_charter_fee'] : '' }}"
                                    />
                                </td>
                            </tr>

                            <tr>
                                <td>@lang("model.LandlordContract.agency_service_fee")</td>
                                <td>
                                    <input
                                        class="form-control form-control-sm"
                                        type="text"
                                        name="agency_service_fee"
                                        value="{{ isset($data["agency_service_fee"]) ? $data['agency_service_fee'] : '' }}"
                                    />
                                </td>
                            </tr>

                            <tr>
                                <td>@lang("model.LandlordContract.rent_collection_frequency")</td>
                                <td>
                                    <input
                                        class="form-control form-control-sm"
                                        type="text"
                                        name="rent_collection_frequency"
                                        value="{{ isset($data["rent_collection_frequency"]) ? $data['rent_collection_frequency'] : '' }}"
                                    />
                                </td>
                            </tr>
                            <tr>
                                <td>@lang("model.LandlordContract.rent_collection_time")</td>
                                <td>
                                    <input
                                        class="form-control form-control-sm"
                                        type="text"
                                        name="rent_collection_time"
                                        value="{{ isset($data["rent_collection_time"]) ? $data['rent_collection_time'] : '' }}"
                                    />
                                </td>
                            </tr>
                            <tr>
                                <td>@lang("model.LandlordContract.rent_adjusted_date")</td>
                                <td>
                                    <input
                                        class="form-control form-control-sm"
                                        type="date"
                                        name="rent_adjusted_date"
                                        value="{{ isset($data["rent_adjusted_date"]) ? $data['rent_adjusted_date'] : '' }}"
                                    />
                                </td>
                            </tr>
                            <tr>
                                <td>@lang("model.LandlordContract.adjust_ratio")</td>
                                <td>
                                    <input
                                        class="form-control form-control-sm"
                                        type="text"
                                        name="adjust_ratio"
                                        value="{{ isset($data["adjust_ratio"]) ? $data['adjust_ratio'] : '' }}"
                                    />
                                </td>
                            </tr>
                            <tr>
                                <td>@lang("model.LandlordContract.deposit_month_count")</td>
                                <td>
                                    <input
                                        class="form-control form-control-sm"
                                        type="text"
                                        name="deposit_month_count"
                                        value="{{ isset($data["deposit_month_count"]) ? $data['deposit_month_count'] : '' }}"
                                    />
                                </td>
                            </tr>
                            <tr>
                                <td>@lang("model.LandlordContract.is_collected_by_third_party")</td>
                                <td>
                                    <input type="hidden" value="0" name="is_collected_by_third_party"/>
                                    <input
                                        type="checkbox"
                                        name="is_collected_by_third_party"
                                        value="1"
                                        {{ isset($data["is_collected_by_third_party"]) ? ($data['is_collected_by_third_party'] ? 'checked' : '') : '' }}
                                    />
                                </td>
                            </tr>   
                            <tr>
                                <td>@lang("model.LandlordContract.is_notarized")</td>
                                <td>
                                    <input type="hidden" value="0" name="is_notarized"/>
                                    <input
                                        type="checkbox"
                                        name="is_notarized"
                                        value="1"
                                        {{ isset($data["is_notarized"]) ? ($data['is_notarized'] ? 'checked' : '') : '' }}
                                    />
                                </td>
                            </tr>    
                            <tr>
                                <td>@lang("model.LandlordContract.bank_code")</td>
                                <td>
                                    <input
                                        class="form-control form-control-sm"
                                        type="text"
                                        name="bank_code"
                                        value="{{ isset($data["bank_code"]) ? $data['bank_code'] : '' }}"
                                    />
                                </td>
                            </tr> 
                            <tr>
                                <td>@lang("model.LandlordContract.branch_code")</td>
                                <td>
                                    <input
                                        class="form-control form-control-sm"
                                        type="text"
                                        name="branch_code"
                                        value="{{ isset($data["branch_code"]) ? $data['branch_code'] : '' }}"
                                    />
                                </td>
                            </tr> 
                            <tr>
                                <td>@lang("model.LandlordContract.account_name")</td>
                                <td>
                                    <input
                                        class="form-control form-control-sm"
                                        type="text"
                                        name="account_name"
                                        value="{{ isset($data["account_name"]) ? $data['account_name'] : '' }}"
                                    />
                                </td>
                            </tr>   
                            <tr>
                                <td>@lang("model.LandlordContract.account_number")</td>
                                <td>
                                    <input
                                        class="form-control form-control-sm"
                                        type="text"
                                        name="account_number"
                                        value="{{ isset($data["account_number"]) ? $data['account_number'] : '' }}"
                                    />
                                </td>
                            </tr>   
                            <tr>
                                <td>@lang("model.LandlordContract.invoice_collection_method")</td>
                                <td>
                                    <input
                                        class="form-control form-control-sm"
                                        type="text"
                                        name="invoice_collection_method"
                                        value="{{ isset($data["invoice_collection_method"]) ? $data['invoice_collection_method'] : '' }}"
                                    />
                                </td>
                            </tr>  
                            <tr>
                                <td>@lang("model.LandlordContract.invoice_collection_number")</td>
                                <td>
                                    <input
                                        class="form-control form-control-sm"
                                        type="text"
                                        name="invoice_collection_number"
                                        value="{{ isset($data["invoice_collection_number"]) ? $data['invoice_collection_number'] : '' }}"
                                    />
                                </td>
                            </tr> 
                            <tr>
                                <td>@lang("model.LandlordContract.invoice_mailing_address")</td>
                                <td>
                                    <input
                                        class="form-control form-control-sm"
                                        type="text"
                                        name="invoice_mailing_address"
                                        value="{{ isset($data["invoice_mailing_address"]) ? $data['invoice_mailing_address'] : '' }}"
                                    />
                                </td>
                            </tr>                          
                            <tr>
                                <td>@lang("model.LandlordContract.commissioner_id")</td>
                                <td>
                                    <select 
                                        data-toggle="selectize" 
                                        data-table="user" 
                                        data-text="name" 
                                        data-value="id" 
                                        data-selected="{{ isset($data["commissioner_id"]) ? $data['commissioner_id'] : '0' }}"
                                        name="commissioner_id"
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
@endsection
