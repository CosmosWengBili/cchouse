@php
    $tableId = "model-{$model_name}-{$layer}-" . rand();

    $appendLandlordQueryString = (function ($key, $value) {
        $routeName = request()->route()->getName();
        $appendRouteName = ['landlords.show'];
        $canAppend = ! is_null($value) && in_array($routeName, $appendRouteName);
        return $canAppend
            ? [ $key => $value ]
            : [];
    }) ('landlord_id', $data['id'] ?? null);

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
        <a class="btn btn-sm btn-success my-3" href="{{ route( 'landlordContracts.create', $appendLandlordQueryString) }}">建立</a>
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
                                @if (is_array($value) && $key === 'building')
                                    <td>{{ $value['address'] }}</td>
                                @elseif (is_array($value) && $key === 'landlords')
                                    @php
                                    $landlordNames = collect($value)->map(function ($item, $key) {
                                        return collect($item)->only(['name'])->toArray();
                                    });
                                    @endphp
                                    <td> {{ $landlordNames->implode('name', ',') }}</td>
                                @else
                                    {{-- an even nested resource array --}}
                                    <td>@include('shared.helpers.value_helper', ['value' => $value])</td>
                                @endif
                            @endforeach
                            <td>
                                <a class="btn btn-success" href="{{ route( Str::camel($layer) . '.show', $object['id']) }}?with=landlords;building;documents">查看</a>
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
