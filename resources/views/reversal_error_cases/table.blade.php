<div class="card">
    <div class="card-body table-responsive">
        <h2>
            @if($model_name == null)
                {{$layer}}
            @else
                @lang("model.{$model_name}.{$layer}")
            @endif
        </h2>
        {{-- you should handle the empty array logic --}}
        @if ($objects->count() == 0)
            <h3>尚無紀錄</h3>
        @else
            <form data-target="#reversal_error_case" data-toggle="datatable-query">
                <div class="query-box">
                </div>
                <i class="fa fa-plus-circle" data-toggle="datatable-query-add"></i>
                <input type="submit" class="btn btn-sm btn-primary" value="搜尋">
            </form>

            <table id="reversal_error_case" class="display table" style="width:100%">
                <thead>
                @php
                    $model_name = ucfirst(Str::camel(substr($layer, 0, -1)));
                @endphp
                @foreach ( array_keys($objects[0]->toArray()) as $field)
                    <th>@lang("model.{$model_name}.{$field}")</th>
                @endforeach
                <th>功能</th>
                </thead>
                <tbody>
                {{-- all the records --}}
                @foreach ( $objects as $object )
                    <tr>
                        {{-- render all attributes --}}
                        @foreach($object->toArray() as $key => $value)
                            {{-- an even nested resource array --}}
                            <td>@include('shared.helpers.value_helper', ['value' => $value])</td>
                        @endforeach
                        <td>
                            @if(
                                $object->payLog &&
                                (
                                    $object->payLog->loggable_type == 'App\TenantPayment' ||
                                    $object->payLog->loggable_type == 'App\TenantElectricityPayment'
                                )
                            )
                                @php
                                    $payment = $object->payLog->loggable;
                                    $contractId = $payment->tenant_contract_id ?? null;
                                @endphp

                                @if($contractId)
                                    <a class="btn btn-info"
                                       href="{{ route('tenantContracts.show', $contractId) }}?with=tenant;room;deposits;debtCollections;tenantPayments;tenantElectricityPayments;payLogs;documents">
                                        查看
                                    </a>
                                @endif
                            @endif
                            <a class="btn btn-success js-pass" href="#" data-id='{{$object['id']}}'>已確認</a>
                            <a class="btn btn-danger jquery-postback" data-method="delete"
                               href="{{ route( Str::camel($layer) . '.show', $object['id']) }}">刪除</a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>

<script>
    $('.js-pass').click(function () {
        var id = $(this).data('id')
        $.post(`/reversalErrorCases/${id}/pass`, {_method: 'put'})
            .then(response => {
                if (response) {
                    location.reload();
                } else {
                    alert('確認失敗')
                }
            })
    })
</script>
<script>
    renderDataTable(["#reversal_error_case"]);
</script>
