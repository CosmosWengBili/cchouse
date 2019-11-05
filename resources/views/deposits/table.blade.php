@php
    $tableId = "model-{$model_name}-{$layer}-" . rand();
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
            <a class="btn btn-sm btn-success my-3" href="{{ route( Str::camel($layer) . '.create') }}">建立</a>
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
                                <button class="btn btn-info" data-type="轉履保" data-deposit-id="{{ $object['id'] }}">轉履保</button>
                                <button class="btn btn-info" data-type="結案" data-invoicing-amount="{{ $object['invoicing_amount'] }}" data-deposit-id="{{ $object['id'] }}">結案</button>
                                <a class="btn btn-success" href="{{ route( Str::camel($layer) . '.show', $object['id']) }}?with=tenantContract;tenantContract.room;tenantContract.room.building">查看</a>
                                <a class="btn btn-primary" href="{{ route( Str::camel($layer) . '.edit', $object['id']) }}">編輯</a>
                                <a class="btn btn-danger jquery-postback" data-method="delete" data-fill-delete-reason="true" href="{{ route( Str::camel($layer) . '.destroy', $object['id']) }}">刪除</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>

<div class="modal" tabindex="-1" id="deposit-modal" role="dialog">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <form action="" method="post">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">結案</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <ul class="nav nav-tabs justify-content-center" id="myTab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="return-tab" data-toggle="tab" href="#return" role="tab" aria-controls="return" aria-selected="true">退訂</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="confiscate-tab" data-toggle="tab" href="#confiscate" role="tab" aria-controls="confiscate" aria-selected="false">沒訂</a>
                        </li>
                    </ul>
                    <div class="tab-content" id="myTabContent">
                        <div class="tab-pane fade show active" id="return" role="tabpanel" aria-labelledby="return-tab"></div>
                        <div class="tab-pane fade" id="confiscate" role="tabpanel" aria-labelledby="confiscate-tab"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary js-submit">送出</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">取消</button>
                </div>
            </form>
        </div>
    </div>
</div>

<template id="return-template">
    <div>
        <div class="form-group row">
            <label class="col-sm-3 col-form-label">退訂金額</label>
            <div class="col">
                <input type="number" class="form-control" name="deposit_returned_amount">
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-3 col-form-label">退訂日期</label>
            <div class="col">
                <input type="date" class="form-control" name="confiscated_or_returned_date">
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-3 col-form-label">退訂方式</label>
            <div class="col">
                <select class="form-control" name="returned_method">
                    @foreach(config('enums.deposits.returned_method') as $value)
                        <option value="{{$value}}">{{$value}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="form-group row js-returned-serial-number-row">
            <label class="col-sm-3 col-form-label js-cash">退訂單號</label>
            <div class="col">
                <input type="text" class="form-control" name="returned_serial_number">
            </div>
        </div>
        <div class="form-group row js-returned-bank-row" style="display: none;">
            <label class="col-sm-3 col-form-label">退訂銀行</label>
            <div class="col">
                <input type="text" class="form-control" name="returned_bank" disabled>
            </div>
        </div>
        <input type="submit" class="d-none">
    </div>
</template>

<template id="confiscate-template">
    <div>
        @csrf
        <div class="form-group row">
            <label class="col-sm-3 col-form-label">沒訂類型</label>
            <div class="col">
                <select class="form-control" name="confiscate_type">
                    <option value="全額沒定" selected>全額沒定</option>
                    <option value="部分沒定">部分沒定</option>
                </select>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-3 col-form-label">沒定金額</label>
            <div class="col">
                <input type="number" class="form-control" name="deposit_confiscated_amount">
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-3 col-form-label">沒定日期</label>
            <div class="col">
                <input type="date" class="form-control" name="confiscated_or_returned_date">
            </div>
        </div>
        <div class="form-group row js-company-allocation-amount-row" style="display: none;">
            <label class="col-sm-3 col-form-label">公司分配金額</label>
            <div class="col">
                <input type="number" class="form-control" name="company_allocation_amount" disabled>
            </div>
        </div>
        <input type="submit" class="d-none">
    </div>
</template>

<script>
    renderDataTable(["#{{$tableId}}"]);
</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var $closeBtns = $('button[data-type="結案"]');
        var $transformBtns = $('button[data-type="轉履保"]');
        var $modal = $('#deposit-modal');
        var $modalBody = $modal.find('.modal-body');
        var $returnContent = $modalBody.find('#return');
        var $confiscateContent = $modalBody.find('#confiscate');
        var $returnTemplate = $('template#return-template').html();
        var $confiscateTemplate = $('template#confiscate-template').html();
        var tmpAmount = '';

        $closeBtns.on('click', function () {
            var $target = $(this);
            var depositId = $target.data('deposit-id');
            var invoicingAmount = $target.data('invoicing-amount');
            tmpAmount = invoicingAmount;

            // initialize return form
            $returnContent.html($returnTemplate);

            // Initialize confiscated form
            $confiscateContent.html($confiscateTemplate);
            var $form = $modal.find('form');
            $form.find('input[name="deposit_confiscated_amount"]').val(invoicingAmount);

            // set action
            $form.attr('action', '/deposits/' + depositId + '/close');

            $modal.modal('show');
        });

        $modalBody.on('change', 'select[name="returned_method"]', function () {
            var method = $(this).val();
            var $serialNumRow = $modalBody.find('.js-returned-serial-number-row');
            var $bankRow = $modalBody.find('.js-returned-bank-row');

            if (method === '匯款') {
                $serialNumRow.hide();
                $bankRow.show();
                $bankRow.find('input[name="returned_bank"]').attr('disabled', false);
                $serialNumRow.find('input[name="returned_serial_number"]').attr('disabled', true);
            } else {
                $bankRow.hide();
                $serialNumRow.show();
                $bankRow.find('input[name="returned_bank"]').attr('disabled', true);
                $serialNumRow.find('input[name="returned_serial_number"]').attr('disabled', false);
            }
        });

        $modalBody.on('change', 'select[name="confiscate_type"]', function () {
            var method = $(this).val();
            var $companyAllocationAmountRow = $modalBody.find('.js-company-allocation-amount-row');
            var $amountInput = $modalBody.find('input[name="deposit_confiscated_amount"]');
            var $allocationInput = $companyAllocationAmountRow.find('input');

            if (method === '部分沒定') {
                $companyAllocationAmountRow.show();
                $allocationInput.attr('disabled', false);
            } else {
                $companyAllocationAmountRow.hide();
                $modalBody.find('input[name="deposit_confiscated_amount"]').val(tmpAmount);
                $allocationInput.attr('disabled', true);
            }
        });

        $modal.find('.js-submit').on('click', function () {
            var submit = $modalBody.find('input[type="submit"]');
            var returnedAmount = Number($modalBody.find('input[name="deposit_returned_amount"]').val());
            var confiscatedAmount = Number($modalBody.find('input[name="deposit_confiscated_amount"]').val());
            var invoicingAmount = Number(tmpAmount);
            if (returnedAmount + confiscatedAmount !== invoicingAmount) {
                alert('沒訂 + 退訂金額不等於收定金額(' + invoicingAmount +') ，請檢查後再送出。');
                return;
            }

            submit.click();
        });

        // 轉履保
        $transformBtns.on('click', function () {
            if(!confirm('確定轉履保')) { return; }
            var depositId = $(this).data('deposit-id');
            var path = '/deposits/' + depositId + '/transform';

            $.post(path, {}, function (resp) {
                alert('轉換成功');
            })
            .error(function() { alert('轉換失敗'); })

        })
    });
</script>
