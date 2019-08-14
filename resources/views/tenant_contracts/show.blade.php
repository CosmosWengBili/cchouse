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

            @if (!empty($relations))
                {{-- you could propbly have many kinds of nested resources --}}
                @foreach($relations as $relation)
                    <div class="col-6 my-3">
                        {{-- handle first level of the nested resource, leave the others to recursion --}}
                        @php
                            $layer = Str::snake(explode('.', $relation)[0]);
                        @endphp
                        @if ( $layer == 'documents')
                            @include('documents.table', ['objects' => $data[$layer], 'layer' => $layer])
                        @elseif ( in_array( $layer , ['tenant', 'room']) )
                            @include('tenant_contracts.single_table', ['objects' => $data[$layer], 'layer' => $layer])
                        @elseif ( $layer == 'payLogs' )
                            @include($layer . '.table', ['objects' => Arr::collapse(Arr::pluck($data['tenant_payments'], 'pay_logs')), 'layer' => $layer."s"])
                        @else
                            @include($layer . '.table', ['object' => $data[$layer], 'layer' => $layer])
                        @endif
                    </div>
                @endforeach
            @endif
        </div>
    </div>
</div>
@endsection
