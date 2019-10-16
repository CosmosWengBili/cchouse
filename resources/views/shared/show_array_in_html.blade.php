<div>
    @if (! empty($data))
        @foreach($data as $key => $value)

            @if (is_bool($value))
                @include('shared.helpers.value_helper', ['value' => $value])
            @else
                @php
                    $langFromTable = isset($model) ? ucfirst(strtolower(Arr::last(explode('\\', $model)))) : $model_name;
                    $color = '';
                    if (isset($colorfulKey) && in_array($key, $colorfulKey)) {
                        $color = 'text-danger';
                    }
                @endphp
                <div class="m-1">
                    <span class="{{ $color }}">@lang("model.{$langFromTable}.{$key}") :</span>
                    <span class="{{ $color }}">{{ $value }}</span>
                </div>
            @endif

        @endforeach
    @endif
</div>
