@extends('layouts.app')
@section('content')
<div class="container">
    <div class="justify-content-center">
        <div class="row my-3">
            <div class="col-12 card">
                <div class="card-body">
                    <div class="card-title">
                        詳細資料
                        <a class="btn btn-primary" href="{{ route( 'landlordContracts.edit', $data['id']) }}">編輯</a>
                    </div>
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
                </div>
                <hr>

                @component('layouts.tab')
                    {{-- other title of relation pages --}}
                    @slot('relation_titles')
                        @if (!empty($relations))
                            @if (!empty($relations))
                                @foreach($relations as $key => $relation)
                                    @php
                                        if (is_null($model_name)) {
                                            $title = $layer;
                                        } else {
                                            $layer = Str::snake(explode('.', $relation)[0]);
                                            $title = __("model.{$model_name}.{$layer}");
                                        }

                                        $active = $loop->first ? 'active' : '';
                                    @endphp
                                    <li class="nav-item">
                                        <a class="nav-link {{ $active }}" data-toggle="tab" href="#content-{{$key}}">{{$title}}</a>
                                    </li>
                                @endforeach
                            @endif
                        @endif
                    @endslot

                    {{-- other contents of relation pages --}}
                    @slot('relation_contents')
                        {{-- display the next level nested resources --}}
                        @if (!empty($relations))
                            {{-- you could propbly have many kinds of nested resources --}}
                            @foreach($relations as $key => $relation)
                                @php
                                    $layer = Str::snake(explode('.', $relation)[0]);
                                    $active = $loop->first ? 'active' : 'fade';
                                @endphp
                                {{-- handle first level of the nested resource, leave the others to recursion --}}
                                <div class="tab-pane container {{ $active }}" id="content-{{$key}}">
                                    @if ( $layer == 'documents' )
                                        @include('documents.table', ['objects' => $data[$layer], 'layer' => $layer])
                                    @elseif ( $layer == 'landlords' )
                                        @include('landlords.table', ['objects' => $data[$layer], 'layer' => $layer])
                                    @else
                                        @include('landlord_contracts.single_table', ['object' => $data[$layer], 'layer' => $layer."s"])
                                    @endif
                                </div>
                            @endforeach
                        @endif
                    @endslot
                @endcomponent
            </div>
        </div>
    </div>
</div>
@endsection
