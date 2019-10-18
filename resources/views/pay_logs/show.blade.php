@extends('layouts.app')
@section('content')
<div class="container">
    <div class="justify-content-center">
        <div class="row my-3">
            <div class="col-12 card">
                <div class="card-body">
                    <div class="card-title">
                        詳細資料
                        <a href="#" id="js-to-deposit" class="btn btn-primary">設為訂金</a>
                    </div>
                    {{-- for showing the target returned --}}
                    <table class="table table-bordered">
                        @foreach ( $data as $attribute => $value)
                            @continue(is_array($value))
                            <tr>
                                <td>@lang("model.{$model_name}.{$attribute}")</td>
                                <td>@include('shared.helpers.value_helper', ['value' => $value])</td>
                            </tr>
                        @endforeach
                    </table>
                </div>
                <hr>

                @component('layouts.tab')
                    {{-- other title of relation pages --}}
                    @slot('relation_titles')
                        @if (!empty($relations))
                            @foreach($relations as $key => $relation)
                                @php
                                    $layer = getLayer($relation);
                                    $title = __("model.{$model_name}.{$layer}");

                                    $active = $loop->first ? 'active' : '';
                                @endphp
                                <li class="nav-item">
                                    <a class="nav-link {{ $active }}" data-toggle="tab" href="#content-{{$key}}">{{$title}}</a>
                                </li>
                            @endforeach
                        @endif
                    @endslot

                    {{-- other contents of relation pages --}}
                    @slot('relation_contents')
                        {{-- display the next level nested resources --}}
                        @if (!empty($relations))
                            {{-- you could propbly have many kinds of nested resources --}}
                            @foreach($relations as $key => $relation)
                                @php
                                    $active = $loop->first ? 'active' : 'fade';
                                @endphp
                                <div class="tab-pane container {{ $active }}" id="content-{{$key}}">
                                    @php
                                        $layer = Str::snake(explode('.', $relation)[0]);
                                    @endphp
                                    @include($layer . '.table', ['objects' => $data[$layer], 'layer' => $layer])
                                </div>
                            @endforeach
                        @endif
                    @endslot
                @endcomponent
            </div>
        </div>
    </div>
</div>
<script>
    $('#js-to-deposit').click(function(){
        let data = {'model': 'Deposit'}
        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-success mr-2',
                cancelButton: 'btn btn-success'
            },
            buttonsStyling: false
        })

            swalWithBootstrapButtons.fire({
                text: "請確認要轉為何種訂金",
                type: 'warning',
                showCancelButton: true,
                confirmButtonText: '訂金',
                cancelButtonText: '訂金(房東)',
            }).then((result) => {
                if (result.value) {
                    data.subject = '訂金'
                }
                else{
                    data.subject = '訂金(房東)'
                }
                $.post("{{ route('payLogs.changeLoggable', $data['id']) }}", data)
                    .then(response => {
                        if (response) {
                            location.reload();
                        } else {
                            alert('更新失敗')
                        }
                    })
            })
    })
</script>
@endsection
