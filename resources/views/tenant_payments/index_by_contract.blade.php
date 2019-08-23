@extends('layouts.app')
@section('content')
<input id="csrf" type="hidden" value={{ csrf_token() }}>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12 my-4">
            @include('tenant_payments.tabs', ['by' => 'contract'])
            {{-- for showing multiple types of entries returned --}}
            @foreach ( $data as $type => $entries)
                @include('tenant_payments.table', ['objects' => $entries, 'layer' => $type])
            @endforeach
        </div>
    </div>
</div>
@endsection