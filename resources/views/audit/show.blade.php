@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12 mt-5">
                <div class="card">
                    <div class="card-body table-responsive">
                        <div class="card-title">
                            詳細資料
                        </div>
                        {{-- for showing the target returned --}}
                        <table class="table table-bordered">
                            @foreach ( $data as $attribute => $value)
                                @continue(is_array($value))
                                <tr>
                                    <td>@lang("model.{$model_name}.{$attribute}")</td>
                                    <td>{{ $value }}</td>
                                </tr>
                            @endforeach
                        </table>
                    </div>
                </div>

                {{-- display the next level nested resources --}}
                <div>
                    @if (!empty($relations))
                        {{-- you could propbly have many kinds of nested resources --}}
                        @foreach($relations as $relation)
                            <div class="col-md-8">
                                {{-- handle first level of the nested resource, leave the others to recursion --}}
                                @php
                                    $layer = explode('.', $relation)[0];
                                @endphp

                                @include('audit.table', ['objects' => $data[$layer], 'layer' => $layer])
                            </div>
                        @endforeach
                    @endif
                </div>

            </div>
        </div>
    </div>
@endsection
