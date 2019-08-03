@extends('layouts.app')

@section('content')
<div class="container">
    <div>uploading to {{$model}}</div>

    <a href="{{'/example/'.$model}}" target="_blank">Get an upload example</a>

    <form action="{{'/import/'.$model}}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="file" name="excel">
        <button type="submit">Submit</button>
    </form>

    @if (session('status'))
        <div>
            {{ session('status') }}
        </div>
    @endif
</div>
@endsection
