@extends('layouts.app')
@section('content')
    @include('layouts.form_error')

@php
    $tenantContractId = Request::get('tenantContractId') ?? $data['tenant_contract_id'] ?? '';
@endphp

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
                            <tbody>
                                <tr>
                                    <td>租客合約</td>
                                    <td>
                                        <select
                                            class="form-control form-control-sm"
                                            name="tenant_contract_id"
                                            data-toggle="selectize"
                                            data-table="tenant_contract"
                                            data-text="id"
                                            data-selected="{{ $tenantContractId ?? '' }}"
                                        >
                                        </select>
                                    </td>
                                    <td>科目</td>
                                    <td>
                                        <select
                                            class="form-control form-control-sm"
                                            name="subject"
                                            data-toggle="selectize"
                                            data-table="tenant_payments"
                                            data-text="subject"
                                            data-value="subject"
                                            data-selected="{{ $data['subject'] ?? '' }}"
                                        >
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td>應繳時間</td>
                                    <td>
                                        <input
                                            type="date"
                                            name="due_time"
                                            class="form-control form-control-sm"
                                            value="{{ $data['due_time'] ?? '' }}"
                                        />
                                    </td>
                                    <td>費用</td>
                                    <td>
                                        <input
                                            type="number"
                                            name="amount"
                                            class="form-control form-control-sm"
                                            value="{{ $data['amount'] ?? '' }}"
                                        />
                                    </td>
                                </tr>
                                <tr>
                                    <td>是否已沖銷</td>
                                    <td>
                                        <input type="hidden" value="0" name="is_charge_off_done"/>
                                        <input
                                            type="checkbox"
                                            name="is_charge_off_done"
                                            value="1"
                                            {{ ($data['sealed_registered'] ?? false) ? 'checked' : '' }}
                                        />
                                    </td>
                                    <td>沖銷日期</td>
                                    <td>
                                        <input
                                            type="date"
                                            name="charge_off_date"
                                            class="form-control form-control-sm"
                                            value="{{ $data['charge_off_date'] ?? '' }}"
                                        />
                                    </td>
                                </tr>
                                <tr>
                                    <td>收取者</td>
                                    <td>
                                        <select
                                            class="form-control form-control-sm"
                                            name="collected_by"
                                        >
                                            @foreach(config('enums.tenant_payments.collected_by') as $value)
                                                <option
                                                    value="{{$value}}"
                                                    {{ ($data['collected_by'] ?? '') == $value ? 'selected' : '' }}
                                                >
                                                    {{$value}}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>是否顯示在報表</td>
                                    <td>
                                        <input type="hidden" value="0" name="is_visible_at_report"/>
                                        <input
                                            type="checkbox"
                                            name="is_visible_at_report"
                                            value="1"
                                            {{ ($data['is_visible_at_report'] ?? false) ? 'checked' : '' }}
                                        />
                                    </td>
                                </tr>
                                <tr>
                                    <td>備註</td>
                                    <td colspan="3">
                                        <textarea name="comment" class="form-control" rows="15">{{  $data['comment'] ?? '' }}</textarea>
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
                due_time: {
                    required: true
                },
                amount: {
                    required: true
                },
                collected_by: {
                    required: true
                },
            };

            const messages = {
                due_time: {
                    required: '必須輸入'
                },
                amount: {
                    required: '必須輸入'
                },
                collected_by: {
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
