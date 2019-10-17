@php
    $user = Auth::User();
    $tenantContractIds = \App\TenantContract::select('id')->pluck('id')->toArray();
    $userIds = \App\User::select('id')->pluck('id')->toArray();
    $isManageGroup = Auth::User()->belongsToGroup('管理組');

    $isCreate = request()->routeIs('maintenances.create');
@endphp

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
                    <form action="{{$action}}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method($method)

                        <h3 class="mt-3">基本資料</h3>
                        @if ($isCreate)
                            {{-- Show some items --}}
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
                                    <td>@lang("model.Maintenance.reported_at")</td>
                                    <td>
                                        <input
                                            class="form-control form-control-sm set-date"
                                            type="date"
                                            name="reported_at"
                                            value="{{ $data['reported_at'] ?? '' }}"
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
                                    <td>@lang("model.Maintenance.incident_details")</td>
                                    <td>
                                        <input
                                            class="form-control form-control-sm"
                                            type="text"
                                            name="incident_details"
                                            value="{{ $data['incident_details'] ?? '' }}"
                                        />
                                    </td>
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
                                    <td></td>
                                    <td></td>
                                </tr>
                                </tbody>
                            </table>
                        @else
                            {{-- Show all items --}}
                            <table class="table table-bordered">
                                <tbody>
                                <tr>
                                    <td>@lang("model.Maintenance.tenant_contract_id")</td>
                                    <td>
                                        <input
                                            class="form-control form-control-sm"
                                            type="text"
                                            name="tenant_contract_id"
                                            readonly
                                            value="{{ $data['tenant_contract_id'] ?? '' }}"
                                        />
                                    </td>
                                    <td>@lang("model.Maintenance.reported_at")</td>
                                    <td>
                                        <input
                                            class="form-control form-control-sm set-date"
                                            type="date"
                                            name="reported_at"
                                            readonly
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
                                    <td>@lang("model.Maintenance.commissioner_id")</td>
                                    <td>
                                        <select
                                            name="commissioner_id"
                                            readonly
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
                                    <td>@lang("model.Maintenance.service_comment")</td>
                                    <td>
                                        <input
                                            class="form-control form-control-sm"
                                            type="text"
                                            name="service_comment"
                                            readonly
                                            value="{{ $data['service_comment'] ?? '' }}"
                                        />
                                    </td>
                                </tr>
                                <tr>
                                    <td>@lang("model.Maintenance.status")</td>
                                    <td>
                                        <input type="hidden" id="old_status" value="{{ $data['status'] ?? '' }}">
                                        <input type="hidden" id="afford_by" value="{{ $data['afford_by'] ?? '' }}">
                                        <input type="hidden" id="group" value="{{ $isManageGroup ? '':  'account'}}">
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
                                    <td>@lang("model.Maintenance.incident_details")</td>
                                    <td>
                                        <input
                                            class="form-control form-control-sm"
                                            type="text"
                                            name="incident_details"
                                            readonly
                                            value="{{ $data['incident_details'] ?? '' }}"
                                        />
                                    </td>
                                </tr>
                                <tr>
                                    <td>@lang("model.Maintenance.incident_type")</td>
                                    <td>
                                        <select
                                            name="incident_type"
                                            readonly
                                            class="form-control form-control-sm"
                                            value="{{ $data['incident_type'] ?? ''}}"
                                        >
                                            @foreach(config('enums.maintenance.incident_type') as $value)
                                                <option value="{{$value}}">{{$value}}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>@lang("model.Maintenance.work_type")</td>
                                    <td>
                                        <select
                                            name="work_type"
                                            readonly
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
                                    <td>@lang("model.Maintenance.is_printed")</td>
                                    <td>
                                        <input type="hidden" value="0" name="is_printed"/>
                                        <input
                                            type="checkbox"
                                            name="is_printed"
                                            value="1"
                                            {{ isset($data["is_printed"]) ? ($data['is_printed'] ? 'checked' : '') : '' }}
                                        />
                                    </td>
                                </tr>

                                </tbody>
                            </table>
                        @endif
                        <h3 class="mt-3">照片</h3>
                        @include('documents.inputs', ['documentType' => 'picture', 'documents' => $data['pictures']])
                        <button class="mt-5 btn btn-success submit" type="button">送出</button>
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

        const $submitButton = $('button.submit');
        $submitButton.click(function () {
            const data = {
                tenant_contract_id: $('input[name=tenant_contract_id]').val(),
                work_type: $('select[name=work_type]').val(),
            };
            $submitButton.prop('disabled', 'disabled');

            if ("{{ !$isCreate ? 'true' : 'false' }}" === 'true') {
                // 是編輯則直接送出
                $('form').submit();
            } else {
                $.post('{{ route('maintenances.check') }}', data)
                    .then(response => {
                        if (response) {
                            // 表示沒有同工種 直接送出
                            $('form').submit();
                        } else {
                            // 表示有同工種 會詢問使用者要建或不要建，要建才會真的送出
                            if (confirm('三個月內已有同工種維護清潔的表單，是否再次建立？')) {
                                $('form').submit();
                            }
                        }
                        $submitButton.prop('disabled', '');
                    })
            }

        });
    })();
</script>
    <script id="validation">

        $(document).ready(function () {
            $.validator.addMethod("checkAffordBy", function(value, element) {
                const isStatusChangedFromCompleteToCancel = $('#old_status').val() === '案件完成' && value === '已取消';
                const isAffordByLandlord = $('#afford_by').val() === '房東';
                const isAccountGroup = $('#group').val() === 'account';

                if (isStatusChangedFromCompleteToCancel && isAffordByLandlord && !isAccountGroup) {
                    return false;
                }
                return true;
            }, "負擔方為『房東』的資料，只有帳務組，可以將狀態從『案件完成』變成『已取消』");

            const rules = {
                tenant_contract_id: {
                    required: true
                },
                reported_at: {
                    required: true
                },
                commissioner_id: {
                    required: true
                },
                service_comment: {
                    required: true
                },
                incident_details: {
                    required: true
                },
                incident_type: {
                    required: true
                },
                work_type: {
                    required: true
                },
                status: {
                    "checkAffordBy": true,
                }
            };

            const messages = {
                tenant_contract_id: {
                    required: '必須輸入'
                },
                reported_at: {
                    required: '必須輸入'
                },
                commissioner_id: {
                    required: '必須輸入'
                },
                service_comment: {
                    required: '必須輸入'
                },
                incident_details: {
                    required: '必須輸入'
                },
                incident_type: {
                    required: '必須輸入'
                },
                work_type: {
                    required: '必須輸入'
                },
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
