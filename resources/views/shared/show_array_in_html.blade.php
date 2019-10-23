<div>
    @if (! empty($data))
        @foreach($data as $key => $value)
            @if (is_bool($value))
                @include('shared.helpers.value_helper', ['value' => $value])
            @elseif ( $key === "command" )
                <div class="m-1">
                    <span>指令 : {{ $value }}</span>
                </div>                
            @elseif ( isset($model) )
                @php
                    $langFromTable = ucfirst(strtolower(Arr::last(explode('\\', $model))));
                    $color = '';
                    if (isset($colorfulKey) && in_array($key, $colorfulKey)) {
                        $color = 'text-danger';
                    }
                @endphp
                <div class="m-1">
                    <span class="{{ $color }}">@lang("model.{$langFromTable}.{$key}") :</span>
                    <span class="{{ $color }}">{{ $value }}</span>
                </div>
            @else
                <div class="border p-2 mb-1">
                @foreach($value as $value_name => $value_value)
                    <span >@lang("model.General.{$value_name}") :</span>
                    <span>{{ $value_value }}</span><br>
                @endforeach
                </div>                
            @endif

        @endforeach
    @endif
</div>
