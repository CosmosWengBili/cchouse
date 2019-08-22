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
            </div>

            @if (in_array('tenantContract', $relations))
                <div class="col-6 my-3">
                    @include('tenant_contracts.table', ['objects' => [Arr::except($data['tenant_contract'], 'room')], 'layer' => 'tenantContracts'])
                </div>
            @endif

            @if (in_array('tenantContract.room', $relations))
                <div class="col-6 my-3">
                    @include('rooms.table', ['objects' => [Arr::except($data['tenant_contract']['room'], 'building')], 'layer' => 'rooms'])
                </div>
            @endif

            @if (in_array('tenantContract.room.building', $relations))
                <div class="col-6 my-3">
                    @include('buildings.table', ['objects' => [$data['tenant_contract']['room']['building']], 'layer' => 'buildings'])
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
