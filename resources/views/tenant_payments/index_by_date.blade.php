@extends('layouts.app')
@section('content')
<input id="csrf" type="hidden" value={{ csrf_token() }}>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12 my-4">
            @include('tenant_payments.tabs', ['by' => 'date'])

            <form class="my-3" method="get">
                <div class="form-group w-50 d-flex">
                    <input type="hidden" name="by" value="date" />
                    <input type="date" name="start_date" class="form-control form-control-sm" value="{{ Request::get('start_date') ?? '' }}">
                    <div class="font-weight-bold mx-3" style="line-height: 41px;">至</div>
                    <input type="date" name="end_date" class="form-control form-control-sm" value="{{ Request::get('end_date') ?? '' }}">
                    <button class="btn btn-success btn-sm m-1" type="submit">查詢</button>
                </div>
            </form>

            @if(Request::get('start_date') && Request::get('end_date'))

            @else
                <h2 class="text-center my-5">請選選擇查詢區間</h2>
            @endif
        </div>
    </div>
</div>
@endsection
