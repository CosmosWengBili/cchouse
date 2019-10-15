@php
    $resource = Str::camel(Str::plural($layer));
    $model = ucfirst(Str::camel(Str::singular($layer)));

    // 匯入或匯出需要帶到server上的參數 是一個以 key-value 表示的陣列
    // 例如qs 要傳送 status= 案件完成 陣列表示為
    // ['status'=>'案件完成']
    $queryString = '';
    if (isset($qs) && is_array($qs)) {
        $queryString = makeArrayToQueryString($qs);
    }

@endphp

<a class="btn btn-sm btn-secondary my-3" href="#" data-toggle="modal" data-target="#import-{{$model}}">匯入 Excel</a>
@if(is_null($parentId))
    <a class="btn btn-sm btn-secondary my-3" href="/export/{{$model}}{{ $queryString==='' ? '' : "?{$queryString}" }}">匯出 Excel</a>
@else
    <a class="btn btn-sm btn-secondary my-3" href="/export/{{ $parentModel }}/{{  $parentId }}/{{$layer}}{{ $queryString==='' ? '' : "?{$queryString}" }}">匯出 Excel</a>
@endif

@include('shared.import_modal', ['model' => $model])
