@php
    $tableId = "model-{$model_name}-{$layer}-" . rand();
    if($layer == "active_contracts"){
        $layer = "tenant_contracts";
    }
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
        <a class="btn btn-sm btn-success my-3" href="{{ route( 'rooms.create') }}">建立</a>
        <a class="btn btn-sm btn-secondary my-3" href="#" data-toggle="modal" data-target="#import-{{$layer}}">匯入 Excel</a>
        <a class="btn btn-sm btn-secondary my-3" href="/export/{{Str::camel(substr($layer, 0, -1))}}">匯出 Excel</a>
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
                        $model_name = ucfirst(Str::camel(Str::Singular($layer)));
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
                                <td> {{$value}}</td>
                            @endforeach
                            <td>
                                <a class="btn btn-success" href="{{ route( Str::camel(Str::plural($layer)) . '.show', $object['id']) }}?with=tenantContracts;keys;appliances;landlordPayments;landlordOtherSubjects;documents">查看</a>
                                <a class="btn btn-primary" href="{{ route( Str::camel(Str::plural($layer)) . '.edit', $object['id']) }}">編輯</a>
                                <a class="btn btn-danger jquery-postback" data-method="delete" href="{{ route( Str::camel(Str::plural($layer)) . '.show', $object['id']) }}">刪除</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>
@include('shared.import_modal', ['layer' => $layer])
<script>
    renderDataTable(["#{{$tableId}}"]);
</script>
