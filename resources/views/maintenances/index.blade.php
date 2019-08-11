@php
    $user = Auth::user();
    $statuses = [];
    if ($user->belongsToGroup('帳務組')) {
        $statuses = array_filter(\App\Maintenance::STATUSES, function ($key) {
            return $key == 'done' || $key == 'request';
        }, ARRAY_FILTER_USE_KEY);;
    } else if ($user->belongsToGroup('管理組')){
        $statuses = \App\Maintenance::STATUSES;
    }

    $workTypes = \App\Maintenance::WORK_TYPES;
@endphp

@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col">
            <ul class="nav nav-tabs justify-content-center" id="status-tabs" role="tablist">
                @foreach($statuses as $statusKey => $name)
                    <li class="nav-item {{ $loop->first ? 'active' : ''  }}">
                        <a
                            class="nav-link {{ $loop->first ? 'active' : ''  }}"
                            data-toggle="tab"
                            href="#{{$statusKey}}-pane"
                            role="tab"
                        >
                            {{ $name }}
                        </a>
                    </li>
                @endforeach
            </ul>
            <div class="tab-content pt-0">
                @foreach($statuses as $statusKey => $name)
                    <div class="tab-pane fade {{ $loop->first ? 'show active' : ''  }}" id="{{$statusKey}}-pane" role="tabpanel">
                        <div class="card">
                            <ul class="nav nav-tabs justify-content-center" id="status-tabs" role="tablist">
                                @foreach($workTypes as $workTypeKey => $name)
                                    <li class="nav-item {{ $loop->first ? 'active' : ''  }}">
                                        <a
                                            class="nav-link {{ $loop->first ? 'active' : ''  }}"
                                            data-toggle="tab"
                                            href="#{{ $statusKey }}-{{ $workTypeKey }}-pane"
                                            role="tab"
                                        >
                                            {{ $name }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                            <div class="tab-content">
                                @foreach($workTypes as $workTypeKey => $name)
                                    @php
                                        $maintenances =
                                            Arr::has($groupedMaintenances, "{$statusKey}.{$workTypeKey}") ?
                                            $groupedMaintenances[$statusKey][$workTypeKey] : [];
                                    @endphp
                                    <div class="tab-pane fade {{ $loop->first ? 'show active' : ''  }}" id="{{ $statusKey }}-{{ $workTypeKey }}-pane" role="tabpanel">
                                        <div class="table-responsive">
                                            @if(count($maintenances) > 0)
                                                <table id="users" class="display table" style="width:100%">
                                                    <thead>
                                                    @foreach ( array_keys($maintenances[0]) as $field)
                                                        <th>@lang("model.Maintenance.{$field}")</th>
                                                    @endforeach
                                                    <th>功能</th>
                                                    </thead>
                                                    <tbody>
                                                    {{-- all the records --}}
                                                    @foreach ( $maintenances as $maintenance )
                                                        <tr>
                                                            {{-- render all attributes --}}
                                                            @foreach($maintenance as $key => $value)
                                                                {{-- an even nested resource array --}}
                                                                <td> {{ $value }}</td>
                                                            @endforeach
                                                            <td>
                                                                <a class="btn btn-success btn-xs" href="{{ route( 'maintenances.show', $maintenance['id']) }}">查看</a>
                                                                <a class="btn btn-primary btn-xs" href="{{ route( 'maintenances.edit', $maintenance['id']) }}">編輯</a>
                                                                <a class="btn btn-danger btn-xs jquery-postback" data-method="delete" href="{{ route('maintenances.show', $maintenance['id']) }}">刪除</a>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                    </tbody>
                                                </table>
                                            @else
                                                <h3 class="text-center">暫無資料</h3>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
