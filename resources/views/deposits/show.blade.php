@extends('layouts.app')
@section('content')
<div class="container">
    <div class="justify-content-center">
        <div class="row my-3">
            <div class="col-12 card">
                <div class="card-body">
                    <div class="card-title">
                        詳細資料
                    </div>
                    {{-- for showing the target returned --}}
                    <table class="table table-bordered">
                        @foreach ( $data as $attribute => $value)
                            @continue(is_array($value))
                            <tr>
                                <td>@lang("model.{$model_name}.{$attribute}")</td>
                                <td>
                                    @if(is_bool($value))
                                        {{ $value ? '是' : '否' }}
                                    @else
                                        {{ $value }}
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </table>
                </div>

                <hr>

                @component('layouts.tab')
                    {{-- other title of relation pages --}}
                    @slot('relation_titles')
                        @if (!empty($relations))
                            @foreach($relations as $key => $relation)
                                @php
                                    $layer = Str::snake(explode('.', $relation)[0]);
                                    $title = __("model.{$model_name}.{$layer}");

                                    $active = $loop->first ? 'active' : '';
                                @endphp
                                <li class="nav-item">
                                    <a class="nav-link {{ $active }}" data-toggle="tab" href="#content-{{$key}}">{{$title}}</a>
                                </li>
                            @endforeach
                        @endif
                    @endslot

                    {{-- other contents of relation pages --}}
                    @slot('relation_contents')
                        {{-- display the next level nested resources --}}
                        @if (!empty($relations))
                            <div class="tab-pane container active" id="content-0">
                                @if (in_array('tenantContract', $relations))
                                    @include('tenant_contracts.table', ['objects' => [Arr::except($data['tenant_contract'], 'room')], 'layer' => 'tenantContracts'])
                                @endif
                            </div>
                            <div class="tab-pane container fade" id="content-1">
                                @if (in_array('tenantContract.room', $relations))
                                    @include('rooms.table', ['objects' => [Arr::except($data['tenant_contract']['room'], 'building')], 'layer' => 'rooms'])
                                @endif
                            </div>
                            <div class="tab-pane container fade" id="content-2">
                                @if (in_array('tenantContract.room.building', $relations))
                                    @include('buildings.table', ['objects' => [$data['tenant_contract']['room']['building']], 'layer' => 'buildings'])
                                @endif
                            </div>
                        @endif
                    @endslot
                @endcomponent
            </div>

        </div>
    </div>
</div>
@endsection
