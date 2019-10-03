@extends('layouts.app')
@section('content')

<div class="container">
    <div class="justify-content-center">
        <div class="row">
            <div class="col-12 p-3">
                <div class="card">
                    <div class="card-body">
                        <div class="card-title">
                            詳細資料
                            <a class="btn btn-primary" href="{{ route( 'debtCollections.edit', $data['id']) }}">編輯</a>
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
                </div>
            </div>

            {{-- display the next level nested resources --}}

            @if (!empty($relations))
                {{-- you could propbly have many kinds of nested resources --}}
                @foreach($relations as $relation)
                    <div class="col-6 my-3">
                        {{-- handle first level of the nested resource, leave the others to recursion --}}
                        @php
                            $layer = Str::snake(explode('.', $relation)[0]);
                            $layerPlural = Str::plural($layer);

                            $objects = null;
                            if (isset($data[$layer][0])) {
                                $objects = $data[$layer];
                            } elseif (isset($data[$layer]['id'])) {
                                $objects = [$data[$layer]];
                            }
                        @endphp
                        @include($layerPlural . '.table', ['objects' => $objects, 'layer' => $layerPlural])
                    </div>
                @endforeach
            @endif
        </div>
    </div>
</div>
@endsection
