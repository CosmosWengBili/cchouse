<div class="card">
    <div class="card-body">
        <h2>{{$layer}}</h2>
        {{-- you should handle the empty array logic --}}
        @if (empty($objects))
            <h3>nothing here</h3>
        @else
            <form data-target="#audit-logs" data-toggle="datatable-query">
                <div class="query-box">
                </div>
                <i class="fa fa-plus-circle" data-toggle="datatable-query-add"></i>
                <input type="submit" class="btn btn-sm btn-primary" value="搜尋">
            </form>

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
                            @if(is_array($value))
                                <td style="min-width:500px">
                                    @include('audit.table', ['objects' => $value, 'layer' => $key])
                                </td>
                            @else
                                <td> {{ $value }}</td>
                            @endif
                        @endforeach
                        <td>
                            <a class="btn btn-success" href="{{ route( Str::camel($layer) . '.show', $object['id']) }}">查看</a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>
<script>
    renderDataTable(["#audit-logs"], {
        scrollX: true,
    })
</script>