
<div class="card">
    <div class="card-body">
        <h2>{{$layer}}</h2>

        {{-- the route to create this kind of resource --}}
        <a class="btn btn-sm btn-success" href="{{ route( Str::camel($layer) . '.create') }}">建立</a>

        {{-- you should handle the empty array logic --}}
        @if (empty($objects))
            <h3>nothing here</h3>
        @else
            <form data-target="#users" data-toggle="datatable-query">
                <div class="query-box">
                </div>
                <i class="fa fa-plus-circle" data-toggle="datatable-query-add"></i>
                <input type="submit" class="btn btn-sm btn-primary" value="搜尋">
            </form>

            <table id="users" class="display table" style="width:100%">
                <thead>
                    <?php $model_name = substr($layer, 0, -1) ?>
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
                                @if(is_array($value))
                                    <td style="min-width:500px">
                                        @include('users.table', ['objects' => $value, 'layer' => $key])
                                    </td>
                                @else
                                    <td> {{ $value }}</td>
                                @endif
                            @endforeach
                            <td>
                                <a class="btn btn-success" href="{{ route( Str::camel($layer) . '.show', $object['id']) }}">查看</a>
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
    renderDataTable(["#users"])
</script>