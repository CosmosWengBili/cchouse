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

            @if (in_array('room', $relations))
                <div class="col-6 my-3">
                    @include('rooms.table', ['objects' => [$data['room']], 'layer' => 'rooms'])
                </div>
            @endif

            @if (in_array('tenant', $relations))
                <div class="col-6 my-3">
                    @include('tenants.table', ['objects' => [$data['tenant']], 'layer' => 'tenants'])
                </div>
            @endif

            @if (in_array('maintenances', $relations))
                <div class="col-6 my-3">
                    @include('maintenances.table', ['objects' => $data['maintenances'], 'layer' => 'maintenances'])
                </div>
            @endif

            @if (in_array('deposits', $relations))
                <div class="col-6 my-3">
                    @include('deposits.table', ['objects' => $data['deposits'], 'layer' => 'deposits'])
                </div>
            @endif
            
            @if (in_array('debtCollections', $relations))
                <div class="col-6 my-3">
                    @include('debt_collections.table', ['objects' => $data['debt_collections'], 'layer' => 'debtCollections'])
                </div>
            @endif

            @if (in_array('tenantPayments', $relations))
                <div class="col-6 my-3">
                    @include('tenant_payments.table', ['objects' => $data['tenant_payments'], 'layer' => 'tenantPayments'])
                </div>
            @endif

            @if (in_array('tenantElectricityPayments', $relations))
                <div class="col-6 my-3">
                    @include('tenant_electricity_payments.table', ['objects' => $data['tenant_electricity_payments'], 'layer' => 'tenantElectricityPayments'])
                </div>
            @endif
            
            @if (in_array('tenantPayments.payLogs', $relations))
                <div class="col-6 my-3">
                    @include('pay_logs.table', ['objects' => Arr::collapse(Arr::pluck($data['tenant_payments'], 'pay_logs')), 'layer' => 'payLogs'])
                </div>
            @endif

        </div>
    </div>
</div>
@endsection
