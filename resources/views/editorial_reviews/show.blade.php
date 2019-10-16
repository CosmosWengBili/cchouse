@extends('layouts.app')
@section('content')
<div class="container">
    <div class="justify-content-center">
        <div class="row my-3">
            <div class="col-12 card">
                <div class="card-body">
                    <div class="card-title">
                        詳細資料
                    </div>
                    {{-- for showing the target returned --}}
                    <div class="row">
                        @foreach ( $data as $attribute => $value)
                            @if(is_array($value) && ($attribute === 'original_value' || $attribute === 'edit_value'))
                                <div class="col-3 border py-2 font-weight-bold">@lang("model.{$model_name}.{$attribute}")</div>
                                <div class="col-3 border py-2">
                                    @include('shared.show_array_in_html', [
                                        'data' => $value,
                                        'model' => $data['editable_type'],
                                        'colorfulKey' => array_keys($data['diffs'])
                                    ])
                                </div>
                            @elseif(is_array($value))
                            @else
                                <div class="col-3 border py-2 font-weight-bold">@lang("model.{$model_name}.{$attribute}")</div>
                                <div class="col-3 border py-2">
                                    @include('shared.helpers.value_helper', ['value' => $value])
                                    @if ($attribute === 'status')
                                        @if ($value === '待審核')
                                            <button id="isPass" type="button" class="btn btn-xs btn-github">
                                                <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                                通過
                                            </button>
                                            <button id="isNotPass" type="button" class="btn btn-xs btn-danger">
                                                <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                                不通過
                                            </button>
                                        @endif
                                    @endif
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $('#isPass').click(function () {
        if (confirm('確認通過？')) {
            const $button = $(this);
            $button.find('span').removeClass('d-none');
            $button.prop('disabled', 'disabled');
            $.post("{{ route('editorialReviews.pass', $data['id']) }}", {_method: 'put'})
                .then(response => {
                    if (response) {
                        location.reload();
                    } else {
                        alert('審核失敗')
                    }
                })
                .always(() => {
                    $button.find('span').addClass('d-none');
                });
        }
    });

    $('#isNotPass').click(function () {
        if (confirm('確認不通過？')) {
            const $button = $(this);
            $button.find('span').removeClass('d-none');
            $button.prop('disabled', 'disabled');
            $.post("{{ route('editorialReviews.notPass', $data['id']) }}", {_method: 'put'})
                .then(response => {
                    if (response) {
                        location.reload();
                    } else {
                        alert('審核失敗')
                    }
                })
                .always(() => {
                    $button.find('span').addClass('d-none');
                });
        }
    });
</script>
@endsection
