@php
    $user = Auth::user();
    $statuses = [];
    $isAccountGroup = false;
    if ($user->belongsToGroup('帳務組')) {
        $statuses = array_filter(\App\Maintenance::STATUSES, function ($key) {
            return $key == 'done' || $key == 'request';
        }, ARRAY_FILTER_USE_KEY);;
        $isAccountGroup = true;
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
                                                <div class="mb-3">
                                                    @if($isAccountGroup && $statusKey == 'request')
                                                        <select class="form-control d-inline-block js-who-undertake" style="width: 100px;">
                                                            <option value="cchouse" selected>兆基負擔</option>
                                                            <option value="landlord">房東負擔</option>
                                                        </select>
                                                        <button type="button" class="btn btn-info btn-xs js-apply-undertake">套用</button>
                                                    @endif
                                                </div>
                                                <table id="{{ $statusKey }}-{{ $workTypeKey }}-table" class="display table" style="width:100%">
                                                    <thead>
                                                        @if($isAccountGroup && $statusKey == 'request')
                                                            <th>
                                                                <input type="checkbox" class="js-select-all">
                                                            </th>
                                                        @endif
                                                        @foreach ( array_keys($maintenances[0]) as $field)
                                                            <th>@lang("model.Maintenance.{$field}")</th>
                                                        @endforeach
                                                    <th>功能</th>
                                                    </thead>
                                                    <tbody>
                                                        {{-- all the records --}}
                                                        @foreach ( $maintenances as $maintenance )
                                                            <tr>
                                                                @if($isAccountGroup && $statusKey == 'request')
                                                                    <td>
                                                                        <input type="checkbox" class="js-checkbox" value="{{ $maintenance['id'] }}">
                                                                    </td>
                                                                @endif
                                                                {{-- render all attributes --}}
                                                                @foreach($maintenance as $key => $value)
                                                                    {{-- an even nested resource array --}}
                                                                    <td> {{ $value }}</td>
                                                                @endforeach
                                                                <td>
                                                                    <a class="btn btn-success btn-xs" href="{{ route( 'maintenances.show', $maintenance['id']) }}?with=tenant;room.building">查看</a>
                                                                    <a class="btn btn-primary btn-xs" href="{{ route( 'maintenances.edit', $maintenance['id']) }}">編輯</a>
                                                                    <a class="btn btn-danger btn-xs jquery-postback" data-method="delete" href="{{ route('maintenances.show', $maintenance['id']) }}">刪除</a>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                                <script>
                                                    renderDataTable(["#{{ $statusKey }}-{{ $workTypeKey }}-table"]);
                                                    @if($isAccountGroup && $statusKey == 'request')
                                                        (function () {
                                                            const $table = $("#{{ $statusKey }}-{{ $workTypeKey }}-table");
                                                            $table.find('.js-select-all').on('change', function () {
                                                                const checked = $(this).prop('checked');
                                                               $table.find('.js-checkbox').prop('checked', checked);
                                                            });

                                                            const targets = {  cchouse: '兆基', landlord: '房東', };
                                                            $('.js-apply-undertake').on('click', function () {
                                                               const $checkboxes = $table.find('.js-checkbox:checked');
                                                               if($checkboxes.length === 0) {
                                                                   alert('請至少選擇一列');
                                                                   return;
                                                               }
                                                               const who = $('.js-who-undertake').val();
                                                               if (!confirm(`確定套用『${targets[who]}負擔』至所選的案件上嗎？（注意此操作不可逆）`)) {
                                                                   return;
                                                               }

                                                               const maintenanceIds = $checkboxes.map(function () { return $(this).val(); }).toArray();
                                                               $.post('/maintenances/markDone', { who: who, maintenanceIds: maintenanceIds }, function () {
                                                                   location.reload();
                                                               })
                                                            });
                                                        })();
                                                    @endif
                                                </script>
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
