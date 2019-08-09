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

            <div class="col-6 my-3">
                @include('keys.single_table', ['object' => $data['keeper'], 'layer' => "users"])
            </div>
            <div class="col-6 my-3">
                @include('keys.single_table', ['object' => $data['room'], 'layer' => "rooms"])
            </div>
            <div class="col-6 my-3">
                @include('key_requests.table', ['objects' => $data['key_requests'], 'layer' => "key_requests", 'key_id' => $data['id']])
            </div>

        </div>
    </div>
</div>
@endsection
