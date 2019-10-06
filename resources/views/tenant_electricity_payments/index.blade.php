@extends('layouts.app')
@section('content')
<input id="csrf" type="hidden" value={{ csrf_token() }}>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12 my-4">
            {{-- for showing multiple types of entries returned --}}
            @foreach ( $data as $layer => $entries)
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
                        <p>僅顯示有效合約中，電費繳款方式為【公司代付】的電費單</p>

                        {{-- the route to create this kind of resource --}}
                        <a class="btn btn-sm btn-success my-3" href="{{ route( Str::camel($layer) . '.create') }}">建立</a>
                        @include('shared.import_export_buttons', ['layer' => $layer, 'parentModel' => $model_name, 'parentId' => $data['id'] ?? null])
                        <a class="btn btn-sm btn-danger my-3" href="#" data-toggle="modal" data-target='#send-report-to-all-by-sms-model'>電費報表全部發送</a>

                        {{-- you should handle the empty array logic --}}
                        @if (empty($entries))
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
                                        $model_name = ucfirst(Str::camel(Str::singular($layer)));
                                    @endphp
                                    @foreach ( array_keys($entries[0]) as $field)
                                        <th>@lang("model.{$model_name}.{$field}")</th>
                                    @endforeach
                                    <th>功能</th>
                                </thead>
                                <tbody>
                                    {{-- all the records --}}
                                    @foreach ( $entries as $object )
                                        <tr>
                                            {{-- render all attributes --}}
                                            @foreach($object as $key => $value)
                                                {{-- an even nested resource array --}}
                                                @if($key === 'currentBalance')
                                                    <td
                                                        style="color: {{ $value < 0 ? 'red' : 'black' }}"
                                                    >
                                                        {{ $value }}
                                                    </td>
                                                @else
                                                    <td>@include('shared.helpers.value_helper', ['value' => $value])</td>
                                                @endif
                                            @endforeach
                                            <td>
                                                <a class="btn btn-success" href="{{ route( Str::camel(Str::plural($layer)) . '.show', $object['id']) }}?with=building;room;tenantPayments;tenantElectricityPayments;payLogs">查看</a>
                                                <a class="btn btn-primary" href="{{ route( Str::camel(Str::plural($layer)) . '.edit', $object['id']) }}">編輯</a>
                                                <a class="btn btn-danger jquery-postback" data-method="delete" href="{{ route( Str::camel(Str::plural($layer)) . '.destroy', $object['id']) }}">刪除</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endif
                    </div>
                </div>
                <script>
                    renderDataTable(["#{{$tableId}}"]);
                </script>
            @endforeach
        </div>
    </div>
</div>
<div class="modal fade" tabindex="-1" role="dialog" id="send-report-to-all-by-sms-model">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">發送全部電費報表簡訊<span class="js-fill-id"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="form-group row">
                        <label class="col-2 col-form-label" for="sms-year">年份：</label>
                        <div class="col-10">
                            <select name="year" id="sms-year" class="form-control form-control-sm">
                                @php
                                    $now = \Carbon\Carbon::now();
                                    $currentYear = $now->year;
                                    $currentMonth = $now->month;
                                @endphp
                                @for ($i = 0; $i < 10; $i++)
                                    <option value="{{ $currentYear - $i }}">{{ $currentYear - $i }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-2 col-form-label" for="sms-month">年份：</label>
                        <div class="col-10">
                            <select name="month" id="sms-month" class="form-control">
                                @for ($i = 1; $i <= 12; $i++)
                                    <option value="{{ $i }}" {{ ($currentMonth == $i) ? 'selected="selected"' : '' }}>{{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary js-submit">送出</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">關閉</button>
            </div>
        </div>
    </div>
</div>
<script>
    (function() {
        $('#send-report-to-all-by-sms-model button.js-submit').on('click', function () {
            const year = $('select[name="year"]').val();
            const month = $('select[name="month"]').val();
            const data =  { year: year, month: month };

            $.post("{{ route('tenantElectricityPayments.sendReportSMSToAll') }}", data, function () {
                alert('發送成功');
                $('#send-report-to-all-by-sms-model').modal('hide');
                $('.modal-backdrop').remove();
            }).fail(function () {
                alert('發送失敗');
            })
        })
    })();
</script>
@endsection
