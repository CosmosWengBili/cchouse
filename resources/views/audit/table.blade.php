<div class="card">
    <div class="card-body">
        <h2>{{$layer}}</h2>
        {{-- you should handle the empty array logic --}}
        @if (empty($objects))
            <h3>尚無紀錄</h3>
        @else
            <form data-target="#audit-logs" data-toggle="datatable-query">
                <div class="query-box">
                </div>
                <i class="fa fa-plus-circle" data-toggle="datatable-query-add"></i>
                <input type="submit" class="btn btn-sm btn-primary" value="搜尋">
            </form>
            <div class="table-responsive">
                <table id="audit-logs" class="display table" style="width:100%">
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
                                <td> {{ $value }}</td>
                            @endforeach
                            <td>
                                <a class="btn btn-success" href="{{ route( Str::camel($layer) . '.show', $object['id']) }}">查看</a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
<script>
    renderDataTable(["#audit-logs"])
</script>
