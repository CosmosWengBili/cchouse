{{-- A value helper used for displaying data by its type --}}
@if(is_bool($value))
    {{ config('enums.yes_or_no')[(int)$value] }}
@elseif (is_array($value))
    @include('shared.show_array_in_html', ['data' => $value])
@elseif (strpos($value,'App') === 0)
    {{ __('model.'.explode('\\',$value)[1].'.model_name') }}
@else
    {{ $value }}
@endif
