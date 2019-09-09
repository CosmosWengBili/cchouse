@extends('layouts.app')

@section('content')
    @include('layouts.form_error')
<div class="container">

    <form action="{{$action}}" method="POST">
        @csrf
        @method($method)

        @foreach ( $data as $attribute => $value )
            {{-- handle your own type and empty policy --}}
            @continue(is_array($value))
            @if ($method === 'PUT')
                <span> {{ $attribute }} : </span>
                <input type="text" name="{{$attribute}}" value="{{$value}}">
            @else
                <span> {{ $value }} : </span>
                <input type="text" name="{{$value}}">
            @endif
        @endforeach

        <button type="submit">Submit</button>
    </form>
</div>
@endsection
