@extends('layouts.app')
@section('content')
<div class="container">
    <div class="rol">
        <div class="col">
            @component('layouts.tab')
                {{-- active title --}}
                @slot('main_title')
                    詳細資料
                @endslot

                {{-- active content --}}
                @slot('main_content')
                    {{-- for showing the target returned --}}
                    <table class="table table-bordered">
                            @foreach ( $data as $attribute => $value)
                                @continue(is_array($value))
                                <tr>
                                    <td>@lang("model.{$model_name}.{$attribute}")</td>
                                    <td>
                                        @if(is_bool($value))
                                            {{ $value ? '是' : '否' }}
                                        @else
                                            {{ $value }}
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </table>
                @endslot

                {{-- other title of relation pages --}}
                @slot('relation_titles')
                    @foreach($relations as $key => $relation)
                        @php
                            if (is_null($model_name)) {
                                $title = $layer;
                            } else {
                                $layer = Str::snake(explode('.', $relation)[0]);
                                $title = __("model.{$model_name}.{$layer}");
                            }
                        @endphp
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#content-{{$key}}">{{$title}}</a>
                        </li>
                    @endforeach
                @endslot

                {{-- other contents of relation pages --}}
                @slot('relation_contents')
                    {{-- display the next level nested resources --}}
                    @if (!empty($relations))
                        {{-- you could propbly have many kinds of nested resources --}}
                        @foreach($relations as $key => $relation)
                            <div class="tab-pane container fade" id="content-{{$key}}">
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
@endsection
