@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col">
            <div class="d-flex justify-content-center">
                <ul class="nav nav-tabs">
                    <li class="nav-item">
                        <a class="nav-link{{ $type != 'other' ? ' active' : '' }}" href="{{ route('reversalErrorCases.index') }}">主資料</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link{{ $type == 'other' ? ' active' : '' }}" href="{{ route('reversalErrorCases.index', ['type' => 'other']) }}">無所屬資料繳款紀錄</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="row justify-content-center">
        <div class="col-md-10">
            {{-- for showing multiple types of entries returned --}}
            @foreach ( $data as $type => $entries)
                @include('reversal_error_cases.table', ['objects' => $entries, 'layer' => $type])
            @endforeach
        </div>

    </div>
</div>
@endsection
