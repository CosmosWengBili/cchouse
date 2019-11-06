@php
    $user = Auth::user();
    $statuses = [];
    $isAccountGroup = false;
    $isManagementGroup = false;
    if ($user->belongsToGroup('帳務組')) {
        $statuses = array_filter(\App\Maintenance::STATUSES , function ($key) {
            return $key == 'done' || $key == 'request';
        }, ARRAY_FILTER_USE_KEY);;
        $isAccountGroup = true;
    } else if ($user->belongsToGroup('管理組')){
        $statuses = \App\Maintenance::STATUSES;
        $isManagementGroup = true;
    }

    $workTypes = \App\Maintenance::WORK_TYPES;
@endphp

@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col">
            @component('layouts.tab')
                @slot('relation_titles')
                    @foreach($statuses as $statusKey => $statusName)
                        <li class="nav-item {{ $loop->first ? 'active' : ''  }}">
                            <a
                                class="nav-link {{ $loop->first ? 'active' : ''  }}"
                                data-toggle="tab"
                                href="#{{ $statusKey }}-pane"
                                role="tab"
                            >
                                {{ $statusName }}
                            </a>
                        </li>
                    @endforeach
                @endslot
                @slot('relation_contents')
                    @foreach($statuses as $statusKey => $statusName)
                            @php
                                $active = $loop->first ? 'show active' : 'fade';
                            @endphp
                        <div class="tab-pane container fade {{ $active }}" id="{{$statusKey}}-pane" role="tabpanel">
                            <div class="card">
                                @php
                                    $maintenances = isset($groupedMaintenances[$statusName])
                                        ? $groupedMaintenances[$statusName]
                                        : [];
                                @endphp
                                @empty ($maintenances)
                                    <div class="card-body table-responsive">
                                        <a class="btn btn-sm btn-success my-3" href="{{ route( 'maintenances.create') }}">建立</a>
                                        <h3 class="text-center">暫無資料</h3>
                                    </div>
                                @else
                                    <div class="card-body table-responsive">
                                        <a class="btn btn-sm btn-success my-3" href="{{ route( 'maintenances.create') }}">建立</a>
                                        @include('shared.import_export_buttons', [
                                            'layer' => 'maintenances',
                                            'parentModel' => 'Maintenance',
                                            'parentId' => $data['id'] ?? null,
                                            'qs' => ['status'=>$statusName]
                                        ])
                                        <div class="mb-3">
                                            @if($isAccountGroup && $statusKey == 'request')
                                                <select class="form-control d-inline-block js-who-undertake" style="width: 100px;">
                                                    <option value="cchouse" selected>兆基負擔</option>
                                                    <option value="landlord">房東負擔</option>
                                                </select>
                                                <button type="button" class="btn btn-info btn-xs js-apply-undertake">套用</button>
                                            @elseif ($isManagementGroup && $statusKey == 'done')
                                                <button type="button" class="btn btn-info btn-xs js-is-printed">確認已儲存</button>
                                            @endif
                                        </div>
                                        <form data-target="#{{ $statusKey }}-table" data-toggle="datatable-query">
                                            <div class="query-box">
                                            </div>
                                            <i class="fa fa-plus-circle" data-toggle="datatable-query-add"></i>
                                            <input type="submit" class="btn btn-sm btn-primary" value="搜尋">
                                        </form>

                                        <table id="{{ $statusKey }}-table" class="display table" style="width:100%">
                                            <thead>
                                            @if($isAccountGroup && $statusKey == 'request')
                                                <th>
                                                    <input type="checkbox" class="js-select-all">
                                                </th>
                                            @elseif ($isManagementGroup && $statusKey == 'done')
                                                <th>
                                                    <input type="checkbox" class="js-is-printed-select-all">
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
                                                    @elseif ($isManagementGroup && $statusKey == 'done')
                                                        <td>
                                                            @if (! $maintenance['is_printed'])
                                                                <input type="checkbox" class="js-checkbox" value="{{ $maintenance['id'] }}">
                                                            @endif
                                                        </td>
                                                    @endif
                                                    {{-- render all attributes --}}
                                                    @foreach($maintenance as $key => $value)
                                                        {{-- an even nested resource array --}}
                                                        <td>@include('shared.helpers.value_helper', ['value' => $value])</td>
                                                    @endforeach
                                                    <td>
                                                        <a class="btn btn-success btn-xs" href="{{ route( 'maintenances.show', $maintenance['id']) }}?with=room">查看</a>
                                                        <a class="btn btn-primary btn-xs" href="{{ route( 'maintenances.edit', $maintenance['id']) }}">編輯</a>
                                                        @if($isAccountGroup && $statusKey == 'request')
                                                            <a class="btn btn-success btn-xs js-get-record" href="#" data-id="{{$maintenance['id']}}" data-toggle="modal" data-target="#maintenance-record-model">查看記錄</a>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                        <script>
                                            renderDataTable(["#{{ $statusKey }}-table"]);
                                            @if($isAccountGroup && $statusKey == 'request')
                                            (function () {
                                                const $table = $("#{{ $statusKey }}-table");
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

                                                $('.js-get-record').on('click', function(){
                                                    const id = $(this).data('id')
                                                    $.get('/maintenances/showRecord/' + id, function (maintenances) {
                                                        const $recordTableBody = $('#record-table tbody')
                                                        $recordTableBody.html("")
                                                        let tableElement = "";
                                                        maintenances.forEach(function(maintenance, maintenances_idx){
                                                            tableElement += '<tr>'
                                                            console.log(maintenance)
                                                            Object.keys(maintenance).forEach(function(key, maintenance_idx){
                                                                tableElement += `<td>${maintenance[key]}</td>`
                                                            })
                                                            tableElement += '</tr>'
                                                        })
                                                        $recordTableBody.append(tableElement)
                                                    })
                                                });
                                            })();
                                            @elseif ($isManagementGroup && $statusKey == 'done')
                                            (function () {
                                                const $table = $("#{{ $statusKey }}-table");
                                                $table.find('.js-is-printed-select-all').on('click', function (event) {
                                                    // change event cannot stop propagation
                                                    event.stopPropagation();
                                                    const checked = $(this).prop('checked');
                                                    $table.find('.js-checkbox').prop('checked', checked);
                                                });

                                                $('button.js-is-printed').on('click', function () {
                                                    const $checkboxes = $table.find('.js-checkbox:checked');
                                                    if($checkboxes.length === 0) {
                                                        alert('請至少選擇一列');
                                                        return;
                                                    }
                                                    const who = $('.js-who-undertake').val();
                                                    if (!confirm('確認已儲存？')) {
                                                        return;
                                                    }

                                                    const data = {
                                                        maintenance_ids: $checkboxes.map(function () { return $(this).val(); }).toArray(),
                                                    };
                                                    $.post('{{ route('maintenances.updateIsPrinted') }}', data)
                                                        .then(response => {
                                                            if (response) {
                                                                location.reload();
                                                            } else {
                                                                alert('更新失敗!')
                                                            }
                                                        })
                                                });
                                            })();
                                            @endif
                                        </script>
                                    </div>
                                @endif
                            </div>
                        </div>
                        @include('maintenances.record_modal', ['maintenances' => array_first($groupedMaintenances)])
                        @endforeach
                @endslot
            @endcomponent
        </div>
    </div>
</div>
@endsection
