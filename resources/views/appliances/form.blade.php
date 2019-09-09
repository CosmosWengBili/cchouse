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

                        <h3 class="mt-3">基本資料</h3>
                        <table class="table table-bordered">
                            <tbody>
                                <tr>
                                    <td>@lang("model.Appliance.room_id")</td>
                                    <td>
                                        <select 
                                            data-toggle="selectize" 
                                            data-table="rooms" 
                                            data-text="id" 
                                            data-selected="{{ $data['room_id'] ?? $room_id ?? '0' }}"
                                            name="room_id"
                                            class="form-control form-control-sm" 
                                        >
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td>@lang("model.Appliance.subject")</td>
                                    <td>
                                        <select 
                                            data-toggle="selectize" 
                                            data-table="appliances" 
                                            data-text="subject" 
                                            data-value="subject" 
                                            data-selected="{{ $data['subject'] ?? '0' }}"
                                            name="subject"
                                            class="form-control form-control-sm" 
                                        >
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td>@lang("model.Appliance.spec_code")</td>
                                    <td>
                                        <select 
                                            data-toggle="selectize" 
                                            data-table="appliances" 
                                            data-text="spec_code" 
                                            data-value="spec_code" 
                                            data-selected="{{ $data['spec_code'] ?? 0 }}"
                                            name="spec_code"
                                            class="form-control form-control-sm" 
                                        >
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td>@lang("model.Appliance.vendor")</td>
                                    <td>
                                        <select 
                                            data-toggle="selectize" 
                                            data-table="appliances" 
                                            data-text="vendor" 
                                            data-value="vendor" 
                                            data-selected="{{ $data['vendor'] ?? 0 }}"
                                            name="vendor"
                                            class="form-control form-control-sm" 
                                        >
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td>@lang("model.Appliance.count")</td>
                                    <td>
                                        <input
                                            class="form-control form-control-sm"
                                            type="number"
                                            name="count"
                                            value="{{ $data['count'] ?? '' }}"
                                        />
                                    </td>
                                </tr>
                                <tr>
                                    <td>@lang("model.Appliance.maintenance_phone")</td>
                                    <td>
                                        <input
                                            class="form-control form-control-sm"
                                            type="text"
                                            name="maintenance_phone"
                                            value="{{ $data['maintenance_phone'] ?? '' }}"
                                        />
                                    </td>
                                </tr>
                                <tr>
                                    <td>@lang("model.Appliance.comment")</td>
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
