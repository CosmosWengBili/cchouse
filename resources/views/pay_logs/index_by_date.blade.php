@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col">
            @include('pay_logs.tabs', ['by' => 'date'])
            {{-- for showing multiple types of entries returned --}}
            @foreach ( $data as $type => $entries)
                @include('pay_logs.table', ['objects' => $entries, 'layer' => $type])
            @endforeach
        </div>
    </div>
</div>
@endsection
