@extends('layouts.app')
@section('content')
<div class="container">
    <div class="rol">
        <div class="col">
            <div class="justify-content-center">
                <div class="row my-3">
                    <div class="col-12 card">
                        <div class="card-body">
                            <div class="card-title">
                                詳細資料
                                <a class="btn btn-primary" href="{{ route( 'buildings.edit', $data['id']) }}">編輯</a>
                            </div>

                            {{-- for showing the target returned --}}
                            <div class="row">
                                @foreach ( $data as $attribute => $value)
                                    @continue(is_array($value))
                                    <div class="col-3 border py-2 font-weight-bold">@lang("model.{$model_name}.{$attribute}")</div>
                                    <div class="col-3 border py-2">
                                        @if(is_bool($value))
                                            {{ $value ? '是' : '否' }}
                                        @else
                                            {{ $value }}
                                        @endif
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
                                        $layer = Str::snake(explode('.', $relation)[0]);
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
                                    @foreach($relations as $key => $relation)
                                        @php
                                            $active = $loop->first ? 'active' : 'fade';
                                        @endphp
                                        <div class="tab-pane container {{ $active }}" id="content-{{$key}}">
                                            @php
                                                $layer = Str::snake(explode('.', $relation)[0]);
                                            @endphp
                                            @include($layer . '.table', ['objects' => $data[$layer], 'layer' => $layer])
                                        </div>
                                    @endforeach
                                @endif
                            @endslot
                        @endcomponent

                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
