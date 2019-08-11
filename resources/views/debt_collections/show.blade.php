@extends('layouts.app')
@section('content')

<div class="container">
    <div class="justify-content-center">
        <div class="row">
            <div class="col p-3">
                <ul class="nav nav-tabs justify-content-center">
                    <li class="nav-item active"
                        ><a class="nav-link active" href="#details" data-toggle="tab">詳細資料</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#collector" data-toggle="tab">催收人</a>
                    </li>
                </ul>
                <div class="tab-content pt-0">
                    <div class="tab-pane fade active show" id="details">
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

                        {{-- display the next level nested resources --}}
                        @if (!empty($relations))
                            {{-- you could propbly have many kinds of nested resources --}}
                            @foreach($relations as $relation)
                                <div class="col-10 my-3 offset-1">
                                    {{-- handle first level of the nested resource, leave the others to recursion --}}
                                    @php
                                        $layer = Str::snake(explode('.', $relation)[0]);
                                        $isPlural = Str::plural($layer) == $layer;
                                        $pluralName = Str::plural($layer);
                                        $objects = $isPlural ? $data[$layer] : [$data[$layer]];
                                    @endphp
                                    @include($pluralName . '.table', ['objects' => $objects, 'layer' => $layer])
                                </div>
                            @endforeach
                        @endif
                    </div>
                    <div class="tab-pane fade" id="collector">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title">
                                    催收人
                                </div>
                                {{-- for showing the target returned --}}
                                <table class="table table-bordered">
                                    @foreach ( $collector as $attribute => $value)
                                        @continue(is_array($value))
                                        <tr>
                                            <td>@lang("model.User.{$attribute}")</td>
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
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
