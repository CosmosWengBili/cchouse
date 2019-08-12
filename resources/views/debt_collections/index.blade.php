@extends('layouts.app')
@section('content')
<input id="csrf" type="hidden" value={{ csrf_token() }}>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12 mt-4">
            {{-- for showing multiple types of entries returned --}}
            @foreach ( $data as $type => $entries)
                @include('debt_collections.table', ['objects' => $entries, 'layer' => $type])
            @endforeach
        </div>
    </div>
</div>
@endsection
