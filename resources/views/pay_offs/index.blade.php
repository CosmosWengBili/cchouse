@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="row justify-content-center">
        <div class="col">
            {{-- for showing multiple types of entries returned --}}
            @foreach ( $data as $type => $entries)
            @include('pay_offs.table', ['objects' => $entries, 'layer' => $type])
            @endforeach
        </div>
    </div>
</div>
@endsection
