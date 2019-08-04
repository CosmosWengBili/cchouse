@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8 mt-5">
            <div class="card">
                <div class="card-body">
                    <div class="card-title">
                        新建 / 編輯表單
                    </div>
                    <form action="{{$action}}" method="POST">
                        @csrf
                        @method($method)
                        
                        <table class="table table-bordered">
                        @foreach ( $data as $attribute => $value )
                            {{-- handle your own type and empty policy --}}
                            @continue(is_array($value))
                            <tr>
                                @if ($method === 'PUT')
                                    <td>@lang("model.{$model_name}.{$attribute}")</td>
                                    <td><input class="form-control form-control-sm" type="text" name="{{$attribute}}" value="{{$value}}"></td>
                                @else
                                    <td>@lang("model.{$model_name}.{$value}")</td>
                                    <td><input class="form-control form-control-sm" type="text" name="{{$value}}"></td>
                                @endif
                            </tr>
                        @endforeach
                        </table>
                        <button class="mt-5 btn btn-success" type="submit">送出</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
