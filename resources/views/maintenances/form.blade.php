@php
    $tenantContractIds = \App\TenantContract::select('id')->pluck('id')->toArray();
    $userIds = \App\User::select('id')->pluck('id')->toArray();
    $statuses = \App\Maintenance::STATUSES;
    $workTypes = \App\Maintenance::WORK_TYPES;
    $incidentTypes = \App\Maintenance::INCIDENT_TYPES;
@endphp

@extends('layouts.app')

@section('content')
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
                                    <td>@lang("model.Maintenance.tenant_contract_id")</td>
                                    <td>
                                        <select
                                            data-toggle="selectize"
                                            data-table="tenant_contract"
                                            data-text="id"
                                            data-selected="{{ $data['tenant_contract_id'] ?? $tenant_contract_id ?? 0 }}"
                                            name="tenant_contract_id"
                                            class="form-control form-control-sm"
                                        >
                                            @foreach($tenantContractIds as $tenantContractId)
                                                <option value="{{ $tenantContractId }}">{{ $tenantContractId }}</option>
                                            @endforeach
                                        </select>
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
                                            data-toggle="selectize"
                                            data-table="user"
                                            data-text="name"
                                            data-selected="{{ $data['commissioner_id'] ?? 0 }}"
                                            name="commissioner_id"
                                            class="form-control form-control-sm"
                                        >
                                            @foreach($userIds as $userId)
                                                <option value="{{ $userId }}">{{ $userId }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td>@lang("model.Maintenance.maintenance_staff_id")</td>
                                    <td>
                                        <select
                                            data-toggle="selectize"
                                            data-table="user"
                                            data-text="name"
                                            data-selected="{{ $data['maintenance_staff_id'] ?? 0 }}"
                                            name="maintenance_staff_id"
                                            class="form-control form-control-sm"
                                        >
                                            @foreach($userIds as $userId)
                                                <option value="{{ $userId }}">{{ $userId }}</option>
                                            @endforeach
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
                                            class="form-control form-control-sm"
                                            name="status"
                                            value="{{ $data['status'] ?? '' }}"
                                        />
                                            @foreach($statuses as $value => $name)
                                                <option value="{{$value}}">{{$name}}</option>
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
                                            class="form-control form-control-sm"
                                            name="incident_type"
                                            value="{{ $data['incident_type'] ?? '' }}"
                                        />
                                            @foreach($incidentTypes as $value => $name)
                                                <option value="{{$value}}">{{$name}}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td>@lang("model.Maintenance.work_type")</td>
                                    <td>
                                        <select
                                            class="form-control form-control-sm"
                                            name="work_type"
                                            value="{{ $data['work_type'] ?? '' }}"
                                        />
                                            @foreach($workTypes as $value => $name)
                                                <option value="{{$value}}">{{$name}}</option>
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
                                    <td>@lang("model.Maintenance.invoice_serail_number")</td>
                                    <td>
                                        <input
                                            class="form-control form-control-sm"
                                            type="text"
                                            name="invoice_serail_number"
                                            value="{{ $data['invoice_serail_number'] ?? '' }}"
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

                        <button class="mt-5 btn btn-success" type="submit">送出</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
