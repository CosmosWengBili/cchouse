{{-- A value helper used for displaying data by its type --}}
@if(is_bool($value))
    {{ config('enums.yes_or_no')[(int)$value] }}
@elseif (is_array($value))
    @include('shared.show_array_in_html', ['data' => $value])
@else
    {{ $value }}
@endif
