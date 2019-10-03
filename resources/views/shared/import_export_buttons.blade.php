@php
    $resource = Str::camel(Str::plural($layer));
    $model = ucfirst(Str::camel(Str::singular($layer)));
@endphp

<a class="btn btn-sm btn-secondary my-3" href="#" data-toggle="modal" data-target="#import-{{$model}}">匯入 Excel</a>
@if(is_null($parentId))
    <a class="btn btn-sm btn-secondary my-3" href="/export/{{$model}}">匯出 Excel</a>
@else
    <a class="btn btn-sm btn-secondary my-3" href="/export/{{ $parentModel }}/{{  $parentId }}/{{$layer}}">匯出 Excel</a>
@endif

@include('shared.import_modal', ['model' => $model])
