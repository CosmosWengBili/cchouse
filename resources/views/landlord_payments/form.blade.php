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
                                <td>@lang("model.LandlordPayment.room_id")</td>
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
                                <td>@lang("model.LandlordPayment.subject")</td>
                                <td>
                                    <select 
                                        data-toggle="selectize" 
                                        data-table="landlord_payments" 
                                        data-text="subject" 
                                        data-value="subject"
                                        data-selected="{{ isset($data["subject"]) ? $data['subject'] : '0' }}"
                                        name="subject"
                                        class="form-control form-control-sm" 
                                    >
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td>@lang("model.LandlordPayment.bill_serial_number")</td>
                                <td>
                                    <input
                                        class="form-control form-control-sm"
                                        type="text"
                                        name="bill_serial_number"
                                        value="{{ isset($data["bill_serial_number"]) ? $data['bill_serial_number'] : '' }}"
                                    />
                                </td>
                            </tr>
                            <tr>
                                <td>@lang("model.LandlordPayment.bill_start_date")</td>
                                <td>
                                    <input
                                        class="form-control form-control-sm"
                                        type="date"
                                        name="bill_start_date"
                                        value="{{ isset($data["bill_start_date"]) ? $data['bill_start_date'] : '' }}"
                                    />
                                </td>
                            </tr>
                            <tr>
                                <td>@lang("model.LandlordPayment.bill_end_date")</td>
                                <td>
                                    <input
                                        class="form-control form-control-sm"
                                        type="date"
                                        name="bill_end_date"
                                        value="{{ isset($data["bill_end_date"]) ? $data['bill_end_date'] : '' }}"
                                    />
                                </td>
                            </tr>
                            <tr>
                                <td>@lang("model.LandlordPayment.collection_date")</td>
                                <td>
                                    <input
                                        class="form-control form-control-sm"
                                        type="date"
                                        name="collection_date"
                                        value="{{ isset($data["collection_date"]) ? $data['collection_date'] : '' }}"
                                    />
                                </td>
                            </tr>
    
                            <tr>
                                <td>@lang("model.LandlordPayment.billing_vendor")</td>
                                <td>
                                    <input
                                        class="form-control form-control-sm"
                                        type="text"
                                        name="billing_vendor"
                                        value="{{ isset($data["billing_vendor"]) ? $data['billing_vendor'] : '' }}"
                                    />
                                </td>
                            </tr>

                            <tr>
                                <td>@lang("model.LandlordPayment.amount")</td>
                                <td>
                                    <input
                                        class="form-control form-control-sm"
                                        type="text"
                                        name="amount"
                                        value="{{ isset($data["amount"]) ? $data['amount'] : '' }}"
                                    />
                                </td>
                            </tr>

                            <tr>
                                <td>@lang("model.LandlordPayment.comment")</td>
                                <td>
                                    <input
                                        class="form-control form-control-sm"
                                        type="text"
                                        name="comment"
                                        value="{{ isset($data["comment"]) ? $data['comment'] : '' }}"
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
