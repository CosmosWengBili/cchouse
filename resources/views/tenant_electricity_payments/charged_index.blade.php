@extends('layouts.app')
@section('content')
<input id="csrf" type="hidden" value={{ csrf_token() }}>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12 my-4">
            <div class="d-flex justify-content-center">
                <ul class="nav nav-tabs">
                    <li class="nav-item">
                        <a class="nav-link{{ Request::get('type') != 'charged' ? ' active' : '' }}" href="{{ route('tenantElectricityPayments.index') }}">主資料</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link{{ Request::get('type') == 'charged' ? ' active' : '' }}" href="{{ route('tenantElectricityPayments.index', ['type' => 'charged']) }}">儲值電</a>
                    </li>
                </ul>
            </div>

            {{-- for showing multiple types of entries returned --}}
            @foreach ( $data as $layer => $entries)
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

                        <form class="my-3" method="get">
                            <input type="hidden" name="type" value="charged" />
                            <div class="form-row">
                                <div class="col">
                                    <input type="text" class="form-control form-control-sm" name="room_code" placeholder="房代碼" value="{{ Request::get('room_code') ?? '' }}">
                                </div>
                                <div class="col">
                                    <input type="date" name="start_date" class="form-control form-control-sm" value="{{ Request::get('start_date') ?? '' }}">
                                </div>
                                <div class="col" style="flex: 0 0;">
                                    <span class="font-weight-bold" style="line-height: 41px;">至</span>
                                </div>
                                <div class="col">
                                    <input type="date" name="end_date" class="form-control form-control-sm" value="{{ Request::get('end_date') ?? '' }}">
                                </div>
                                <div class="col">
                                    <button class="btn btn-success btn-sm m-1" type="submit">查詢</button>
                                </div>
                            </div>
                        </form>

                        {{-- you should handle the empty array logic --}}
                        @if (empty($entries))
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
                                        $model_name = ucfirst(Str::camel(Str::singular($layer)));
                                    @endphp
                                    @foreach ( array_keys($entries[0]) as $field)
                                        <th>@lang("model.{$model_name}.{$field}")</th>
                                    @endforeach
                                    <th>功能</th>
                                </thead>
                                <tbody>
                                    {{-- all the records --}}
                                    @foreach ( $entries as $object )
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
                                                    <td>@include('shared.helpers.value_helper', ['value' => $value])</td>
                                                @endif
                                            @endforeach
                                            <td>
                                                <a class="btn btn-success" href="{{ route( Str::camel(Str::plural($layer)) . '.show', $object['id']) }}">查看</a>
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
            @endforeach
        </div>
    </div>
</div>
@endsection
