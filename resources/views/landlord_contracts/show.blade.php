@extends('layouts.app')

@section('content')
<div class="container">

    <div class="row justify-content-center">
        <div class="col-md-8">

            {{-- for showing the target returned --}}
            @foreach ( $data as $attribute => $value)
                @continue(is_array($value))
                <div> {{ $attribute }} : {{ $value }} </div>
            @endforeach
            <br><br>

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

                            {{-- before render: check existence of relation --}}
                            @if (isset($data[$layer]))
                                @include('landlord_contracts.table', ['objects' => $data[$layer], 'layer' => $layer])
                            @else
                                <div>no record</div>
                            @endif
                        </div>
                    @endforeach

                @endif
            </div>

        </div>

    </div>
</div>
@endsection
