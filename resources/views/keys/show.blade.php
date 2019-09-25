@extends('layouts.app')
@section('content')
<div class="container">
    <div class="justify-content-center">
        <div class="row my-3">
            <div class="col-12 card">
                <div class="card-body">
                    <div class="card-title">
                        詳細資料
                        <a class="btn btn-primary" href="{{ route( 'keys.edit', $data['id']) }}">編輯</a>
                    </div>
                    {{-- for showing the target returned --}}
                    <div class="row">
                        @foreach ( $data as $attribute => $value)
                            @continue(is_array($value))
                            <div class="col-3 border py-2 font-weight-bold">@lang("model.{$model_name}.{$attribute}")</div>
                            <div class="col-3 border py-2">
                                @include('shared.helpers.value_helper', ['value' => $value])
                            </div>
                        @endforeach
                    </div>
                </div>

                <hr>

                @component('layouts.tab')
                    {{-- other title of relation pages --}}
                    @slot('relation_titles')
                        @if (!empty($relations))
                            @foreach($relations as $key => $relation)
                                @php
                                    $layer = explode('.', $relation);
                                    $layer = Str::snake(last($layer));
                                    $layer = Str::plural($layer);
                                    $title = __("model.{$model_name}.{$layer}");

                                    $active = $loop->first ? 'active' : '';
                                @endphp
                                <li class="nav-item">
                                    <a class="nav-link {{ $active }}" data-toggle="tab" href="#content-{{$key}}">{{$title}}</a>
                                </li>
                            @endforeach
                        @endif
                    @endslot

                    {{-- other contents of relation pages --}}
                    @slot('relation_contents')
                        {{-- display the next level nested resources --}}
                        @if (!empty($relations))
                            {{-- you could propbly have many kinds of nested resources --}}
                            @if ($data['keeper'])
                                <div class="tab-pane container active" id="content-0">
                                    @include('keys.single_table', ['object' => $data['keeper'], 'layer' => "users"])
                                </div>
                            @endif
                            @if ($data['keeper'])
                                <div class="tab-pane container active" id="content-1">
                                    @include('keys.single_table', ['object' => $data['room'], 'layer' => "rooms"])
                                </div>
                            @endif
                            @if ($data['keeper'])
                                <div class="tab-pane container active" id="content-2">
                                    @include('key_requests.table', ['objects' => $data['key_requests'], 'layer' => "key_requests", 'key_id' => $data['id']])
                                </div>
                            @endif
                        @endif
                    @endslot
                @endcomponent

            </div>
    </div>
</div>
@endsection
