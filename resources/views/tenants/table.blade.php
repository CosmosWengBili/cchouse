@php
    $tableId = "model-{$model_name}-{$layer}";
    $showFunction = Route::has(Str::camel($layer) . '.show') ||
                    Route::has(Str::camel($layer) . '.edit') ||
                    Route::has(Str::camel($layer) . '.destroy');
@endphp

<div class="card">
    <div class="card-body">
        <h2>@lang("model.{$model_name}.{$layer}")</h2>

        {{-- the route to create this kind of resource --}}
{{--        <a class="btn btn-sm btn-success" href="{{ route( Str::camel($layer) . '.create') }}">建立</a>--}}

        {{-- you should handle the empty array logic --}}
        @if (empty($objects))
            <h3>Nothing here</h3>
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
                    @if($showFunction)
                        <th>功能</th>
                    @endif
                </thead>
                <tbody>
                    {{-- all the records --}}
                    @foreach ( $objects as $object )
                        <tr>
                            {{-- render all attributes --}}
                            @foreach($object as $key => $value)
                                {{-- an even nested resource array --}}
                                @if(is_array($value))
                                    <td style="min-width:500px">
                                        @include('tenants.table', ['objects' => $value, 'layer' => $key])
                                    </td>
                                @else
                                    <td> {{ $value }}</td>
                                @endif
                            @endforeach
                            @if($showFunction)
                                <td>
                                    @if(Route::has(Str::camel($layer) . '.show'))
                                        <a class="btn btn-success" href="{{ route( Str::camel($layer) . '.show', $object['id']) }}">查看</a>
                                    @endif
                                    @if(Route::has(Str::camel($layer) . '.edit'))
                                        <a class="btn btn-primary" href="{{ route( Str::camel($layer) . '.edit', $object['id']) }}">編輯</a>
                                    @endif
                                    @if(Route::has(Str::camel($layer) . '.destroy'))
                                        <a class="btn btn-danger jquery-postback" data-method="delete" href="{{ route( Str::camel($layer) . '.destroy', $object['id']) }}">刪除</a>
                                    @endif
                                </td>
                            @endif
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
