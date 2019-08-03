@extends('layouts.app')

@section('content')
<div class="container">

    <div class="row justify-content-center">
        <div class="col-md-8">

            {{-- for showing multiple types of entries returned --}}
            @foreach ( $data as $type => $entries)
                
        
                @include('landlords.table', ['objects' => $entries, 'layer' => $type])

            @endforeach

        </div>

    </div>
</div>
@endsection
