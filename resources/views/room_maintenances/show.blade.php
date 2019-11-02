@extends('layouts.app')
@section('content')

<div class="container">
    <div class="justify-content-center">
        <div class="row">
            <div class="col p-3">
                <div class="card">
                    <div class="card-body">
                        <div class="card-title">
                            詳細資料
                            <a class="btn btn-primary" href="{{ route( 'roomMaintenances.edit', $data['id']) }}">編輯</a>
                        </div>
                        {{-- for showing the target returned --}}
                        <div class="row">
                            @foreach ( $data as $attribute => $value)
                                @continue(is_array($value))
                                <div class="col-3 border py-2 font-weight-bold">@lang("model.{$model_name}.{$attribute}")</div>
                                <div class="col-3 border py-2">
                                    @include('shared.helpers.value_helper', ['value' => $value])
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-5"></div>
                        <div class="card-title">
                            @if (empty($documents))
                                <h3>無圖片</h3>
                            @else
                                <h3>圖片</h3>
                            @endif
                        </div>
                        {{-- for showing the target returned --}}
                        <div class="row">
                            @foreach($documents as $document)
                                <div class="col-lg-3 col-md-4 col-6">
                                    <a href="#" class="d-block mb-4 h-100">
                                        <img class="img-fluid img-thumbnail" src="{{ $document->url() }}" />
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <hr>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
