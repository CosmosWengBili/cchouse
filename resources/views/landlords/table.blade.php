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
        <a class="btn btn-sm btn-success" href="{{ route( 'landlords.create') }}">建立</a>

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
                    <th>功能</th>
                </thead>
                <tbody>
                    {{-- all the records --}}
                    @foreach ( $objects as $object )
                        <tr>
                            {{-- render all attributes --}}
                            @foreach($object as $key => $value)
                                {{-- an even nested resource array --}}
                                <td> {{ $value }}</td>
                            @endforeach
                            <td>
                                <a class="btn btn-success" href="{{ route( Str::camel($layer) . '.show', $object['id']) }}?with=landlordContracts;agents;contactInfos">查看</a>
                                <a class="btn btn-primary" href="{{ route( Str::camel($layer) . '.edit', $object['id']) }}">編輯</a>
                                <a class="btn btn-danger jquery-postback" data-method="delete" href="{{ route( Str::camel($layer) . '.show', $object['id']) }}">刪除</a>
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
