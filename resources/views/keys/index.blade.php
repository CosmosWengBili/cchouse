@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            {{-- for showing multiple types of entries returned --}}
            @foreach ( $data as $type => $entries)
                @include('keys.table', ['objects' => $entries, 'layer' => $type])
            @endforeach
        </div>
    </div>
    <div class="row justify-content-center">
        <div class="col-md-10">
            {{-- for showing multiple types of entries returned --}}
            @foreach ( $owner_data['data'] as $type => $entries)
                @include('keys.owner_table', ['objects' => $entries, 'layer' => $type])
            @endforeach
        </div>
    </div>

</div>
@endsection
