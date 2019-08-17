@php
    $tableId = "model-{$model_name}-{$layer}-" . rand();
@endphp
<ul class="nav nav-tabs">
    <li class="nav-item">
        <a
            class="nav-link {{ $by == 'contract' ? 'active' : '' }}"
            href="{{ route('tenantPayments.index', ['by' => 'contract']) }}"
        >
            By Contract
        </a>
    </li>
    <li class="nav-item">
        <a
            class="nav-link {{ $by == 'time' ? 'active' : '' }}"
            href="{{ route('tenantPayments.index', ['by' => 'time']) }}"
        >
            By Time
        </a>
    </li>
</ul>
<div class="card">
    <div class="card-body table-responsive">
        <h2>
            @if($model_name == null)
               {{$layer}}
            @else
                @lang("model.{$model_name}.{$layer}")
            @endif
        </h2>
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
                                @if($key === 'currentBalance')
                                    <td
                                        style="color: {{ $value < 0 ? 'red' : 'black' }}"
                                    >
                                        {{ $value }}
                                    </td>
                                @else
                                    <td> {{ $value }}</td>
                                @endif
                            @endforeach
                            <td>
                                <a class="btn btn-success" href="{{ route( Str::camel(Str::plural($layer)) . '.show', $object['id']) }}?with=tenantContracts;contactInfos;emergencyContacts;guarantors">查看</a>
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
