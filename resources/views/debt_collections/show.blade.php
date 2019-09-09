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
                            $objects = isset($data[$layer][0]) ? $data[$layer] : [$data[$layer]];
                        @endphp
                        @include($layerPlural . '.table', ['objects' => $objects, 'layer' => $layerPlural])
                    </div>
                @endforeach
            @endif
        </div>
    </div>
</div>
@endsection
