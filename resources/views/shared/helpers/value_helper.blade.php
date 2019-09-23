{{-- A value helper used for displaying data by its type --}}
@if(is_bool($value))
    {{ config('enums.yes_or_no')[(int)$value] }}
@else
    {{ $value }}
@endif
