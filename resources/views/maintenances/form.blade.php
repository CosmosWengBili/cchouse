@php
    $user = Auth::User();
    $tenantContractIds = \App\TenantContract::select('id')->pluck('id')->toArray();
    $userIds = \App\User::select('id')->pluck('id')->toArray();
    $isManageGroup = Auth::User()->belongsToGroup('管理組');
@endphp

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
                                    <td>@lang("model.Maintenance.tenant_contract_id")</td>
                                    <td>
                                        <input
                                            class="form-control form-control-sm"
                                            type="text"
                                            name="tenant_contract_id"
                                            value="{{ $data['tenant_contract_id'] ?? '' }}"
                                        />
                                    </td>
                                </tr>
                                <tr>
                                    <td>@lang("model.Maintenance.reported_at")</td>
                                    <td>
                                        <input
                                            class="form-control form-control-sm"
                                            type="date"
                                            name="reported_at"
                                            value="{{ $data['reported_at'] ?? '' }}"
                                        />
                                    </td>
                                </tr>
                                <tr>
                                    <td>@lang("model.Maintenance.expected_service_date")</td>
                                    <td>
                                        <input
                                            class="form-control form-control-sm"
                                            type="date"
                                            name="expected_service_date"
                                            value="{{ $data['expected_service_date'] ?? '' }}"
                                        />
                                    </td>
                                </tr>
                                <tr>
                                    <td>@lang("model.Maintenance.expected_service_time")</td>
                                    <td>
                                        <input
                                            class="form-control form-control-sm"
                                            type="time"
                                            name="expected_service_time"
                                            value="{{ $data['expected_service_time'] ?? '' }}"
                                        />
                                    </td>
                                </tr>
                                <tr>
                                    <td>@lang("model.Maintenance.dispatch_date")</td>
                                    <td>
                                        <input
                                            class="form-control form-control-sm"
                                            type="date"
                                            name="dispatch_date"
                                            value="{{ $data['dispatch_date'] ?? '' }}"
                                        />
                                    </td>
                                </tr>
                                <tr>
                                    <td>@lang("model.Maintenance.commissioner_id")</td>
                                    <td>
                                        <select
                                            name="commissioner_id"
                                            class="form-control form-control-sm"
                                            data-toggle="selectize" 
                                            data-table="users" 
                                            data-text="name" 
                                            data-selected="{{ $data['commissioner_id'] ?? 0 }}"
                                        >
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td>@lang("model.Maintenance.maintenance_staff_id")</td>
                                    <td>
                                        <select
                                            name="maintenance_staff_id"
                                            class="form-control form-control-sm"
                                            data-toggle="selectize" 
                                            data-table="users" 
                                            data-text="name" 
                                            data-selected="{{ $data['maintenance_staff_id'] ?? 0 }}"
                                        >
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td>@lang("model.Maintenance.closed_date")</td>
                                    <td>
                                        <input
                                            class="form-control form-control-sm"
                                            type="date"
                                            name="closed_date"
                                            value="{{ $data['closed_date'] ?? '' }}"
                                        />
                                    </td>
                                </tr>
                                <tr>
                                    <td>@lang("model.Maintenance.closed_comment")</td>
                                    <td>
                                        <input
                                            class="form-control form-control-sm"
                                            type="text"
                                            name="closed_comment"
                                            value="{{ $data['closed_comment'] ?? '' }}"
                                        />
                                    </td>
                                </tr>
                                <tr>
                                    <td>@lang("model.Maintenance.service_comment")</td>
                                    <td>
                                        <input
                                            class="form-control form-control-sm"
                                            type="text"
                                            name="service_comment"
                                            value="{{ $data['service_comment'] ?? '' }}"
                                        />
                                    </td>
                                </tr>
                                <tr>
                                    <td>@lang("model.Maintenance.status")</td>
                                    <td>
                                        <select
                                            name="status"
                                            class="form-control form-control-sm"
                                            value="{{ $data['status'] ?? ''}}"
                                        >
                                            @foreach(config('enums.maintenance.status') as $value)
                                                @php
                                                    $disabled = ($value == '案件完成' && $isManageGroup) && (isset($data["is_recorded"]) ? ($data['is_recorded'] == true) : true);
                                                @endphp
                                                @if( !$disabled )
                                                    <option value="{{$value}}"
                                                    >{{$value}}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td>@lang("model.Maintenance.incident_details")</td>
                                    <td>
                                        <input
                                            class="form-control form-control-sm"
                                            type="text"
                                            name="incident_details"
                                            value="{{ $data['incident_details'] ?? '' }}"
                                        />
                                    </td>
                                </tr>
                                <tr>
                                    <td>@lang("model.Maintenance.incident_type")</td>
                                    <td>
                                        <select
                                            name="incident_type"
                                            class="form-control form-control-sm"
                                            value="{{ $data['incident_type'] ?? ''}}"
                                        >
                                            @foreach(config('enums.maintenance.incident_type') as $value)
                                                <option value="{{$value}}">{{$value}}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td>@lang("model.Maintenance.work_type")</td>
                                    <td>
                                        <select
                                            name="work_type"
                                            class="form-control form-control-sm"
                                            value="{{ $data['work_type'] ?? ''}}"
                                        >
                                            @foreach(config('enums.maintenance.work_type') as $value)
                                                <option value="{{$value}}">{{$value}}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td>@lang("model.Maintenance.number_of_times")</td>
                                    <td>
                                        <input
                                            class="form-control form-control-sm"
                                            type="number"
                                            name="number_of_times"
                                            value="{{ $data['number_of_times'] ?? '' }}"
                                        />
                                    </td>
                                </tr>
                                <tr>
                                    <td>@lang("model.Maintenance.payment_request_date")</td>
                                    <td>
                                        <input
                                            class="form-control form-control-sm"
                                            type="date"
                                            name="payment_request_date"
                                            value="{{ $data['payment_request_date'] ?? '' }}"
                                        />
                                    </td>
                                </tr>
                                <tr>
                                    <td>@lang("model.Maintenance.closing_serial_number")</td>
                                    <td>
                                        <input
                                            class="form-control form-control-sm"
                                            type="text"
                                            name="closing_serial_number"
                                            value="{{ $data['closing_serial_number'] ?? '' }}"
                                        />
                                    </td>
                                </tr>
                                <tr>
                                    <td>@lang("model.Maintenance.billing_details")</td>
                                    <td>
                                        <input
                                            class="form-control form-control-sm"
                                            type="text"
                                            name="billing_details"
                                            value="{{ $data['billing_details'] ?? '' }}"
                                        />
                                    </td>
                                </tr>
                                <tr>
                                    <td>@lang("model.Maintenance.payment_request_serial_number")</td>
                                    <td>
                                        <input
                                            class="form-control form-control-sm"
                                            type="text"
                                            name="payment_request_serial_number"
                                            value="{{ $data['payment_request_serial_number'] ?? '' }}"
                                        />
                                    </td>
                                </tr>
                                <tr>
                                    <td>@lang("model.Maintenance.cost")</td>
                                    <td>
                                        <input
                                            class="form-control form-control-sm"
                                            type="number"
                                            name="cost"
                                            value="{{ $data['cost'] ?? '' }}"
                                        />
                                    </td>
                                </tr>
                                <tr>
                                    <td>@lang("model.Maintenance.price")</td>
                                    <td>
                                        <input
                                            class="form-control form-control-sm"
                                            type="number"
                                            name="price"
                                            value="{{ $data['price'] ?? '' }}"
                                        />
                                    </td>
                                </tr>
                                <tr>
                                    <td>@lang("model.Maintenance.is_recorded")</td>
                                    <td>

                                        <input type="hidden" value="0" name="is_recorded"/>
                                        <input
                                            type="checkbox"
                                            name="is_recorded"
                                            value="1"
                                            {{ isset($data["is_recorded"]) ? ($data['is_recorded'] ? 'checked' : '') : '' }}
                                        />
                                    </td>
                                </tr>
                                <tr>
                                    <td>@lang("model.Maintenance.comment")</td>
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
                        <button class="mt-5 btn btn-success" type="submit">送出</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    (function () {
        const isManageGroup = {{ $isManageGroup ? 'true' : 'false' }};
        if (!isManageGroup) return;

        const $checkbox = $('input[name="is_recorded"][type="checkbox"]');
        const $options = $('option[value="done"]');
        $checkbox.on('change', function () {
            const checked = $checkbox.prop('checked');
            $options.prop('disabled', checked);
        });
    })();
</script>
@endsection
