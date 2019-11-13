@extends('layouts.app')
@section('content')
<div class="content-wrapper">
    <div class="justify-content-center">
        <div class="row">
            <div class="col-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="card-title">
                            詳細資料
                        </div>
                        {{-- for showing the target returned --}}
                        <div class="row">
                            @foreach ( $data as $attribute => $value)
                            @if ($attribute !== 'payment_detail' )
                            <div class="col-3 border py-2 font-weight-bold">@lang("model.{$model_name}.{$attribute}")
                            </div>
                            <div class="col-3 border py-2">
                                @include('shared.helpers.value_helper', ['value' => $value])
                            </div>
                            @endif
                            @endforeach
                        </div>

                    </div>
                </div>
            </div>
        </div>
        @isset($data['payment_detail'])
        <div class="row">
            <div class="col-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">@lang("model.{$model_name}.payment_detail")</h4>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>科目</th>
                                        <th>金額</th>
                                        <th>收取者</th>
                                        <th>備註</th>
                                        <th>動作</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($data['payment_detail'] as $index => $detail)
                                    <tr>
                                        <td>{{$detail['subject']}}</td>
                                        <td>{{$detail['amount']}}</td>
                                        <td>{{$detail['collected_by']}}</td>
                                        <td>
                                            <input class="form-control" type="text" value="{{$detail['comment']}}"
                                                id="comment-{{$index}}" disabled>
                                        </td>
                                        <td>
                                            <button class="btn btn-outline-primary edit-btn" data-index="{{$index}}"
                                                data-status="edit">
                                                修改
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endisset
    </div>
</div>
@endsection

@push('js')
<link rel="stylesheet" href="{{asset('vendors/jquery-toast-plugin/jquery.toast.min.css')}}">
<script src="{{asset('vendors/jquery-toast-plugin/jquery.toast.min.js')}}"></script>

<script type="text/javascript">
    $(document).ready(function () {
        $('.edit-btn').on('click', function (event) {
            var index = $(this).data('index')
            var status = $(this).data('status')
            var $target = $('#comment-' + index)

            if (status == 'edit') {
                $target.prop('disabled', false)
                $(this).data('status', 'confirm')
                $(this).html('送出')



            } else {
                $target.prop('disabled', true)
                $(this).data('status', 'edit')
                $(this).html('修改')

                var data = {}
                data[index] = {
                    comment: $target.val()
                }
                updateCommentText(data)
            }
        })
    });

    var updateCommentText = function (comment) {
        $api_url = "/payOffs/{{$data['id']}}"

        $.ajax({
            url: $api_url,
            method: 'put',
            data: {
                payment_detail: comment
            },
            success: function (response) {
                $.toast({
                    heading: 'Success',
                    text: response.message,
                    showHideTransition: 'slide',
                    icon: 'success',
                    loaderBg: '#f96868',
                    position: 'top-right'
                })
            },
            error: function (error) {
                var responseJSON = error.responseJSON
                for (const key in responseJSON.errors) {
                    if (responseJSON.errors.hasOwnProperty(key)) {
                        const element = responseJSON.errors[key];
                        $.toast({
                            heading: 'Warning',
                            text: element[0],
                            showHideTransition: 'slide',
                            icon: 'warning',
                            loaderBg: '#57c7d4',
                            position: 'top-right'
                        })
                    }
                }
            }
        })
    }

</script>
@endpush
