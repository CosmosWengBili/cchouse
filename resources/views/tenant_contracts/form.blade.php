@extends('layouts.app')

@section('content')
    @include('layouts.form_error')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12 mt-5">
            <div class="card">
                <div class="card-body">
                    <div class="card-title">
                        @if( (string) request()->route()->getName() == "tenantContracts.extend" )
                            延期續約
                        @else
                            新建 / 編輯表單
                        @endif
                    </div>
                    <form action="{{$action}}" method="POST"  enctype="multipart/form-data">
                        @csrf
                        @method($method)

                        <h3 class="mt-3">基本資料</h3>
                        <table class="table table-bordered">
                            <tbody>
                                <tr>
                                    <td>@lang("model.TenantContract.room_code")</td>
                                    <td>
                                        <select
                                            data-toggle="selectize"
                                            data-table="rooms"
                                            data-text="room_code"
                                            data-selected="{{ $data['room_id'] ?? 0 }}"
                                            name="room_id"
                                            class="form-control form-control-sm"
                                        >
                                        </select>
                                    </td>
                                    <td>@lang("model.TenantContract.tenant_id")</td>
                                    <td>
                                        <select
                                            data-toggle="selectize"
                                            data-table="tenants"
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
                                    <td>@lang("model.TenantContract.contract_start")</td>
                                    <td>
                                        <input
                                            class="form-control form-control-sm"
                                            type="date"
                                            name="contract_start"
                                            value="{{ isset($data['contract_start']) ? \Carbon\Carbon::parse($data['contract_start'])->format('Y-m-d') : '' }}"
                                        />
                                    </td>
                                    <td>@lang("model.TenantContract.overdue_fine")</td>
                                    <td>
                                        <input
                                            class="form-control form-control-sm"
                                            type="number"
                                            name="overdue_fine"
                                            value="{{ $data['overdue_fine'] ?? '' }}"
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
                                            value="{{ isset($data['contract_end']) ? \Carbon\Carbon::parse($data['contract_end'])->format('Y-m-d') : '' }}"
                                        />
                                    </td>
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
                                            placeholder="1日 ~ 31日"
                                            min="1"
                                            max="31"
                                            step="1"
                                        />
                                    </td>
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
                                    <td>@lang("model.TenantContract.comment")</td>
                                    <td colspan="3">
                                        <textarea class="form-control" rows="5" name="comment">{{ $data['comment'] ?? '' }}</textarea>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        @if(Route::current()->getName() != 'tenantContracts.edit')
                            @include('tenant_contracts.payment')
                        @endif

                        <h3 class="mt-3">發票載具檔案</h3>
                        @include('documents.inputs', ['documentType' => 'carrier_file', 'documents' => $data['carrier_files']])

                        <h3 class="mt-3">合約原檔</h3>
                        @include('documents.inputs', ['documentType' => 'original_file', 'documents' => $data['original_files']])

                        <button class="mt-5 btn btn-success" type="submit">送出</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal" id="room-infos" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">房</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>收定日期</th>
                            <th>收訂單號</th>
                            <th>付訂人姓名</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary">確定</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">取消</button>
            </div>
        </div>
    </div>
</div>

<script>
    const qs = window.myQueryString();
    const tenantId = qs.getQueryStrings()['tenant_id'];
    const $tenantId = $('[name="tenant_id"]');
    $tenantId.attr('data-selected', tenantId)

    const roomId = qs.getQueryStrings()['room_id'];
    const $roomId = $('[name="room_id"]');
    roomId && $roomId.attr('data-selected', roomId)
</script>
<script id="validation">

    $(document).ready(function () {

        const rules = {
            contract_serial_number: {
                required: true
            },
            other_rights: {
                required: true
            },
            car_parking_floor: {
                required: true
            },
            car_parking_space_number: {
                required: true
            },
            motorcycle_parking_space_number: {
                required: true,
            },
            motorcycle_parking_count: {
                required: true,
            },
            contract_start: {
                required: true
            },
            contract_end: {
                required: true
            },
            rent: {
                required: true
            },
            rent_pay_day: {
                required: true,
                min: 1,
                max: 31,
            },
            deposit: {
                required:true,
                depositIsLessThanRent:true,
                depositIsEqualDepositPaid:true,
            },
            deposit_paid: {
                required: true
            },
            electricity_price_per_degree: {
                required: true
            },
            electricity_price_per_degree_summer: {
                required: true
            },
            "110v_start_degree": {
                required: true
            },
            overdue_fine:{
                required: true
            }
        };

        const messages = {
            contract_serial_number: {
                required: '必須輸入'
            },
            other_rights: {
                required: '必須輸入'
            },
            car_parking_floor: {
                required: '必須輸入'
            },
            car_parking_space_number: {
                required: '必須輸入'
            },
            motorcycle_parking_space_number: {
                required: '必須輸入',
            },
            motorcycle_parking_count: {
                required: '必須輸入',
            },
            contract_start: {
                required: '必須輸入'
            },
            contract_end: {
                required: '必須輸入'
            },
            rent: {
                required: '必須輸入'
            },
            rent_pay_day: {
                required: '必須輸入',
                min: "日期輸入錯誤 沒有 {0} 日",
                max: "日期輸入錯誤 沒有 {0} 日",
            },
            deposit: {
                required: '必須輸入'
            },
            deposit_paid: {
                required: '必須輸入'
            },
            electricity_price_per_degree: {
                required: '必須輸入'
            },
            electricity_price_per_degree_summer: {
                required: '必須輸入'
            },
            "110v_start_degree": {
                required: '必須輸入'
            },
            overdue_fine: {
                required: '必須輸入'
            },
        };

        $.validator.addMethod("depositIsLessThanRent", function(deposit, element) {
            var rent = parseInt($('[name="rent"]').val())
            deposit  = parseInt(deposit)
            return (deposit - 2*rent) > 0 ? false : true
        }, "不可以超過租金 * 2");

        $.validator.addMethod("depositIsEqualDepositPaid", function(deposit, element) {
            var deposit_paid = parseInt($('[name="deposit_paid"]').val())
            deposit  = parseInt(deposit)
            return (deposit === deposit_paid) ? true : false
        }, "押金 和 押金已繳納 必須一致");

        $('form').validate({
            rules: rules,
            messages: messages,
            errorElement: "em",
            submitHandler:function(form){
                var contract_start = new Date($('[name="contract_start"]').val()).getTime();
                var contract_end = new Date($('[name="contract_end"]').val()).getTime();
                if (contract_end - contract_start < 31536000000) {
                    if (!confirm("請確認是否有漲租金")) {
                        return false;
                    }
                }
                form.submit();
            },
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

<script>
    $(document).ready(function () {
        var firstFlag = true;
        var $modal = $('#room-infos');
        var $selector = $('select[name="room_id"]');
        var $table = $modal.find('table');

        function updateDeposits(records) {
            $table.find('tbody').html('');

            records.forEach(function (record) {
                $modal.find('tbody').append(
                    '<tr>' +
                    '    <td>' + record.deposit_collection_date + '</td>' +
                    '    <td>' + record.deposit_collection_serial_number + '</td>' +
                    '    <td>' + record.payer_name + '</td>' +
                    '</tr>'
                );
            });
        }

        $selector.on('change', function () {
            var roomId = $(this).val();
            if (firstFlag || !roomId) { // 第一次進入頁面時不執行
                firstFlag = false;
                return;
            }

            $.get('/rooms/' + roomId  + '/deposits', function (records) {
                updateDeposits(records);
                $modal.modal('show');
            });
        });

        $modal.find('button.btn-primary').on('click', function () {
            $modal.modal('hide');
        });

        $modal.find('button.btn-secondary').on('click', function () {
            $.each($selector, function (key, elem) { elem.selectize.clear(); });
        });

    });
</script>
@endsection
