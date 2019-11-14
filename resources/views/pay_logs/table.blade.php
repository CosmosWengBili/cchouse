@php
    $tableId = "model-{$model_name}-{$layer}-" . rand();
    $appendCreateParams = (function () use ($data) {
        $routeName = request()->route()->getName();
        switch ($routeName) {
            case 'tenantContracts.show':
                return ['tenantContractId' => $data['id'] ?? null];
            default:
                return [];
        }
    })();
@endphp

<div class="card">
    <div class="card-body table-responsive">
        <h2>
            @if($model_name == null)
               {{$layer}}
            @else
                @lang("model.{$model_name}.{$layer}")
            @endif
        </h2>

        {{-- the route to create this kind of resource --}}
        @if(Route::has(Str::camel($layer) . '.create'))
            <a class="btn btn-sm btn-success my-3" href="{{ route( Str::camel($layer) . '.create', $appendCreateParams) }}">建立</a>
        @endif
        @include('shared.import_export_buttons', ['layer' => $layer, 'parentModel' => $model_name, 'parentId' => $data['id'] ?? null])

        {{-- you should handle the empty array logic --}}
        @if (empty($objects))
            <h3>尚無紀錄</h3>
        @else
            <form data-target="#{{$tableId}}" data-toggle="datatable-query">
                <div class="query-box">
                </div>
                <i class="fa fa-plus-circle" data-toggle="datatable-query-add"></i>
                <input type="submit" class="btn btn-sm btn-primary" value="搜尋">
            </form>

            <table id="{{ $tableId}}" class="display table" style="width:100%">
                <thead>
                    @php
                        $model_name = ucfirst(Str::camel(substr($layer, 0, -1)));
                    @endphp
                    @foreach ( array_keys($objects[0]) as $field)
                        <th>@lang("model.{$model_name}.{$field}")</th>
                    @endforeach
                    <th>功能</th>
                </thead>
                <tbody>
                    {{-- all the records --}}
                    @foreach ( $objects as $object )
                        <tr>
                            {{-- render all attributes --}}
                            @foreach($object as $key => $value)
                                {{-- an even nested resource array --}}
                                <td>@include('shared.helpers.value_helper', ['value' => $value])</td>
                            @endforeach
                            <td>
                                @if($layer == 'tenant_contracts')
                                    <a class="btn btn-info" href="{{ route( Str::camel($layer) . '.show', $object['id']) }}?with=payLogs">繳費記錄</a>
                                @endif

                                @if($layer == 'pay_logs')
                                    <button class="btn btn-outline-github" data-pay-log-id="{{ $object['id'] }}" data-toggle="modal" data-target="#transform-to-deposit-modal">轉為訂金</button>
                                @endif
                                <a class="btn btn-success" href="{{ route( Str::camel($layer) . '.show', $object['id']) }}">查看</a>
                                <a class="btn btn-primary" href="{{ route( Str::camel($layer) . '.edit', $object['id']) }}">編輯</a>
                                <a class="btn btn-danger jquery-postback" data-method="delete" href="{{ route( Str::camel($layer) . '.destroy', $object['id']) }}">刪除</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>

@if($layer == 'pay_logs')
<div class="modal fade" id="transform-to-deposit-modal">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <form action="" method="POST">
                @csrf
                <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="modal-title">轉為訂金</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <!-- Modal body -->
                <div class="modal-body">
                    <table class="table table-bordered">
                        <tr>
                            <td>@lang("model.Deposit.deposit_collection_serial_number")</td>
                            <td>
                                <input
                                    class="form-control form-control-sm"
                                    type="text"
                                    name="deposit_collection_serial_number"
                                    required
                                />
                            </td>
                            <td>@lang("model.Deposit.payer_name")</td>
                            <td>
                                <input
                                    class="form-control form-control-sm"
                                    type="text"
                                    name="payer_name"
                                    required
                                />
                            </td>
                        </tr>
                        <tr>
                            <td>@lang("model.Deposit.payer_certification_number")</td>
                            <td>
                                <input
                                    class="form-control form-control-sm"
                                    type="text"
                                    name="payer_certification_number"
                                    required
                                />
                            </td>
                            <td>@lang("model.Deposit.payer_is_legal_person")</td>
                            <td>
                                <input type="hidden" value="0" name="payer_is_legal_person"/>
                                <input
                                    type="checkbox"
                                    name="payer_is_legal_person"
                                    value="1"
                                />
                            </td>
                        </tr>
                        <td>@lang("model.Deposit.payer_phone")</td>
                        <td>
                            <input
                                class="form-control form-control-sm"
                                type="text"
                                name="payer_phone"
                                required
                            />
                        </td>
                        <td>@lang("model.Deposit.receiver")</td>
                        <td>
                            <select
                                data-toggle="selectize"
                                data-table="users"
                                data-text="name"
                                name="receiver"
                                class="form-control form-control-sm"
                                required
                            >
                            </select>
                        </td>
                        <tr>
                            <td>@lang("model.Deposit.appointment_date")</td>
                            <td colspan="3">
                                <input
                                    class="form-control form-control-sm"
                                    type="date"
                                    name="appointment_date"
                                    required
                                />
                            </td>
                        </tr>
                    </table>
                </div>

                <!-- Modal footer -->
                <div class="modal-footer">
                    <button class="btn btn-sm btn-primary send">送出</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<script>
    $(function () {
        renderDataTable(["#{{$tableId}}"]);

        $('button[data-target="#transform-to-deposit-modal"]').on('click', function () {
           var payLogId = $(this).data('pay-log-id');
           $('#transform-to-deposit-modal form').attr('action', '/payLogs/' + payLogId + '/transformToDeposit')
        });
    });
</script>
