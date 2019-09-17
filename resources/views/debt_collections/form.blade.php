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

                        <h3 class="mt-3">基本資料</h3>
                        <table class="table table-bordered">
                            <tbody>
                                <tr>
                                    <td>@lang("model.DebtCollection.tenant_contract_id")</td>
                                    <td>
                                        <input
                                            class="form-control form-control-sm"
                                            type="text"
                                            name="tenant_contract_id"
                                            value="{{ isset($data["tenant_contract_id"]) ? $data['tenant_contract_id'] : '' }}"
                                        />
                                    </td>
                                    <td>@lang("model.DebtCollection.status")</td>
                                    <td>
                                        <select
                                            class="form-control form-control-sm"
                                            name="status"
                                            value="{{ isset($data["status"]) ? $data['status'] : '' }}"
                                        />
                                            @foreach(config('enums.debt_collections.status') as $value)
                                                <option value="{{$value}}">{{$value}}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td>@lang("model.DebtCollection.details")</td>
                                    <td>
                                        <textarea name="details" class="form-control" rows="15">{{ isset($data["details"]) ? $data['details'] : '' }}</textarea>
                                    </td>
                                    <td>@lang("model.DebtCollection.comment")</td>
                                    <td>
                                        <textarea name="comment" class="form-control" rows="15">{{ isset($data["comment"]) ? $data['comment'] : '' }}</textarea>
                                    </td>
                                </tr>
                                <tr>
                                    <td>@lang("model.DebtCollection.is_penalty_collected")</td>
                                    <td>
                                        {{-- unchecked value for checkbox--}}
                                        <input type="hidden" value="0" name="is_penalty_collected"/>
                                        <input
                                            type="checkbox"
                                            name="is_penalty_collected"
                                            value="1"
                                            {{ isset($data["is_penalty_collected"]) ? ($data['is_penalty_collected'] ? 'checked' : '') : '' }}
                                        />
                                    </td>
                                    <td>@lang("model.DebtCollection.collector_id")</td>
                                    <td>
                                        <select
                                            data-toggle="selectize"
                                            data-table="users"
                                            data-text="name"
                                            data-selected="{{ $data['collector_id'] ?? 0 }}"
                                            name="collector_id"
                                            class="form-control form-control-sm"
                                        >
                                        </select>
                                    </td>
                                </tr>
                            </tbody>
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
                tenant_contract_id: {
                    required: true
                },
                details: {
                    required: true
                },
            };

            const messages = {
                tenant_contract_id: {
                    required: '必須輸入'
                },
                details: {
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
