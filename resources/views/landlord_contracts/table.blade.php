<h2>{{$layer}}</h2>
{{-- the route to create this kind of resource --}}
@if ( Route::has( Str::camel($layer) . '.create') )
    <span>{{ route( Str::camel($layer) . '.create') }}</span>
@else  
    {{-- it's not a model, probably a morph relationship collection --}}
@endif

{{-- you should handle the empty array logic --}}
@if (empty($objects))
    <h3>nothing here</h3>

@elseif($layer === 'building')
    {{-- example: custom hasOne relation render --}}
    <div>
        {{json_encode($objects)}}
    </div>
@else
    <table border="1">
        <thead>
            @foreach ( array_keys($objects[0]) as $field)
                <th>{{ $field }}</th>
            @endforeach
        </thead>
        <tbody>
            {{-- all the records --}}
            @foreach ( $objects as $object )
                <tr>
                    {{-- render all attributes --}}
                    @foreach($object as $key => $value)

                        @if(is_array($value))
                            <td style="min-width:500px">
                                @include('landlord_contracts.table', ['objects' => $value, 'layer' => $key])
                            </td>
                        @else
                            <td> {{ $value }}</td>
                        @endif
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
@endif