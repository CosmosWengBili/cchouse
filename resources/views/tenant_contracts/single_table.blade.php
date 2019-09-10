@php
    $tableId = "model-{$model_name}-{$layer}-" . rand();
    $pluralLayer = Str::plural($layer);
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

        @include('shared.import_export_buttons', ['layer' => $layer, 'parentModel' => $model_name, 'parentId' => $data['id'] ?? null])

        {{-- you should handle the empty array logic --}}
        @if (empty($object))
            <h3>目前沒有資料</h3>
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
                    @foreach ( array_keys($object) as $field)
                        @if ( $field != 'building' )
                        <th>@lang("model.{$model_name}.{$field}")</th>
                        @endif
                    @endforeach
                    <th>功能</th>
                </thead>
                <tbody>
                    {{-- all the records --}}
                    <tr>
                        {{-- render all attributes --}}
                        @foreach($object as $key => $value)
                            {{-- an even nested resource array --}}
                            @if ( $key != 'building' )
                                <td> {{ $value }}</td>
                            @endif
                        @endforeach
                        <td>
                            @if ($layer == 'building')
                                @php
                                    $now = \Carbon\Carbon::now();
                                    $year = $now->year;
                                    $month = $now->month;
                                @endphp
                                <a class="btn btn-info" href="{{ route('buildings.electricityPaymentReport', ['building' => $object['id'], 'year' => $year, 'month' => $month])}}">顯示電費報表</a>
                            @endif
                            <a class="btn btn-success" href="{{ route( Str::camel($pluralLayer) . '.show', $object['id']) }}">查看</a>
                            <a class="btn btn-primary" href="{{ route( Str::camel($pluralLayer) . '.edit', $object['id']) }}">編輯</a>
                            <a class="btn btn-danger jquery-postback" data-method="delete" href="{{ route( Str::camel($pluralLayer) . '.show', $object['id']) }}">刪除</a>
                        </td>
                    </tr>
                </tbody>
            </table>
        @endif
    </div>
</div>
<script>
    renderDataTable(["#{{$tableId}}"]);
</script>
