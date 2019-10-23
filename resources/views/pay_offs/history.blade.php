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
                    <div class="row">
                        @foreach ( $data as $attribute => $value)
                            @if(is_array($value) && ($attribute === 'payment_detail'))
                                <div class="col-3 border py-2 font-weight-bold">@lang("model.{$model_name}.{$attribute}")</div>
                                <div class="col-3 border py-2">
                                    @include('shared.show_array_in_html', ['data' => $value])
                                </div>
                            @else
                                <div class="col-3 border py-2 font-weight-bold">@lang("model.{$model_name}.{$attribute}")</div>
                                <div class="col-3 border py-2">
                                    @include('shared.helpers.value_helper', ['value' => $value])
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection