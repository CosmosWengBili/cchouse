@php
    $startDate = Request::get('start_date') ?? '';
    $endDate = Request::get('end_date') ?? '';
@endphp

@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col">
            @include('pay_logs.tabs', ['by' => 'date'])
            <div class="card">
                <div class="card-body">
                    <form method="GET">
                        <input type="hidden" name="by" value="date">
                        <input type="date" name="start_date" value="{{ $startDate }}">
                        <span class="d-inline-block mx-1">~</span>
                        <input type="date" name="end_date" value="{{ $endDate }}">
                        <input class="btn btn-primary btn-xs"  type="submit" value="送出">
                    </form>
                    {{-- for showing multiple types of entries returned --}}
                    @foreach ( $data as $type => $entries)
                        @include('pay_logs.table', ['objects' => $entries, 'layer' => $type])
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
