@extends('layouts.app')

@section('content')
    @include('layouts.form_error')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12 mt-5">
            <div class="card">
                <div class="card-body">
                    <div class="card-title">
                        新建 / 編輯表單
                    </div>
                    <form action="{{$action}}" method="POST">
                        @csrf
                        @method($method)

                        <table class="table table-bordered">
                        @foreach ( $data as $attribute => $value )
                            {{-- handle your own type and empty policy --}}
                            @continue(is_array($value))
                            <tr>
                                @if ($method === 'PUT')
                                    <td>@lang("model.{$model_name}.{$attribute}")</td>
                                    <td><input class="form-control form-control-sm" type="text" name="{{$attribute}}" value="{{$value}}"></td>
                                @else
                                    <td>@lang("model.{$model_name}.{$value}")</td>
                                    <td><input class="form-control form-control-sm" type="text" name="{{$value}}"></td>
                                @endif
                            </tr>
                        @endforeach
                        </table>
                        <button class="mt-5 btn btn-success" type="submit">送出</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
    <script id="validation">

        $(document).ready(function () {

            const rules = {
                name: {
                    required: true
                },
                email: {
                    required: true
                },
                password: {
                    required: true
                },
                mobile: {
                    required: true
                },
            };

            const messages = {
                name: {
                    required: '必須輸入'
                },
                email: {
                    required: '必須輸入'
                },
                password: {
                    required: '必須輸入'
                },
                mobile: {
                    required: '必須輸入'
                },
            };

            $('form').validate({
                rules: rules,
                messages: messages,
                errorElement: "em",
                errorPlacement: function ( error, element ) {
                    error.addClass( "invalid-feedback" );
                    if ( element.prop( "type" ) === "checkbox" ) {
                        error.insertAfter( element.next( "label" ) );
                    } else {
                        error.insertAfter( element );
                    }
                },
                highlight: function ( element, errorClass, validClass ) {
                    $( element ).addClass( "is-invalid" ).removeClass( "is-valid" );
                },
                unhighlight: function (element, errorClass, validClass) {
                    $( element ).addClass( "is-valid" ).removeClass( "is-invalid" );
                }
            });

        });



    </script>
@endsection
