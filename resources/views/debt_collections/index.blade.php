@extends('layouts.app')
@section('content')
<input id="csrf" type="hidden" value={{ csrf_token() }}>
<div class="container">
    <div class="row justify-content-center">
        <ul class="nav nav-tabs justify-content-center col-12">
            <li class="nav-item active"
                ><a class="nav-link active" href="#all" data-toggle="tab">全部催收案件</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#owner" data-toggle="tab">您的催收案件</a>
            </li>
        </ul>
        <div class="tab-content w-100 pt-0">
            <div class="tab-pane fade active show" id="all">
                <div class="col-md-12">
                    @foreach ( $data as $type => $entries)
                        @include('debt_collections.table', ['objects' => $entries, 'layer' => $type])
                    @endforeach
                </div>
            </div>
            <div class="tab-pane fade" id="owner">
                <div class="col-md-12">
                @foreach ( $owner_data['data'] as $type => $entries)
                    @include('debt_collections.table', ['objects' => $entries, 'layer' => $type])
                @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
