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
                                <td>@lang("model.Tenant.name")</td>
                                <td>
                                    <input
                                        class="form-control form-control-sm"
                                        type="text"
                                        name="name"
                                        value="{{ isset($data["name"]) ? $data['name'] : '' }}"
                                    />
                                </td>
                                <td>@lang("model.Tenant.certificate_number")</td>
                                <td>
                                    <input
                                        class="form-control form-control-sm"
                                        type="text"
                                        name="certificate_number"
                                        value="{{ isset($data["certificate_number"]) ? $data['certificate_number'] : '' }}"
                                    />
                                </td>
                            </tr>
                            <tr>
                                <td>@lang("model.Tenant.is_legal_person")</td>
                                <td>
                                    {{-- unchecked value for checkbox--}}
                                    <input type="hidden" value="0" name="is_legal_person"/>
                                    <input
                                        type="checkbox"
                                        name="is_legal_person"
                                        value="1"
                                        {{ isset($data["is_legal_person"]) ? ($data['is_legal_person'] ? 'checked' : '') : '' }}
                                    />
                                </td>
                                <td>@lang("model.Tenant.line_id")</td>
                                <td>
                                    <input
                                        class="form-control form-control-sm"
                                        type="text"
                                        name="line_id"
                                        value="{{ isset($data["line_id"]) ? $data['line_id'] : '' }}"
                                    />
                                </td>
                            </tr>

                            <tr>
                                <td>@lang("model.Tenant.residence_address")</td>
                                <td>
                                    <input
                                        class="form-control form-control-sm"
                                        type="text"
                                        name="residence_address"
                                        value="{{ isset($data["residence_address"]) ? $data['residence_address'] : '' }}"
                                    />
                                </td>
                                <td>@lang("model.Tenant.company")</td>
                                <td>
                                    <input
                                        class="form-control form-control-sm"
                                        type="text"
                                        name="company"
                                        value="{{ isset($data["company"]) ? $data['company'] : '' }}"
                                    />
                                </td>
                            </tr>

                            <tr>
                                <td>@lang("model.Tenant.job_position")</td>
                                <td>
                                    <input
                                        class="form-control form-control-sm"
                                        type="text"
                                        name="job_position"
                                        value="{{ isset($data["job_position"]) ? $data['job_position'] : '' }}"
                                    />
                                </td>
                                <td>@lang("model.Tenant.company_address")</td>
                                <td>
                                    <input
                                        class="form-control form-control-sm"
                                        type="text"
                                        name="company_address"
                                        value="{{ isset($data["company_address"]) ? $data['company_address'] : '' }}"
                                    />
                                </td>
                            </tr>
                            <tr>
                                <td>@lang("model.Tenant.birth")</td>
                                <td>
                                    <input
                                        class="form-control form-control-sm"
                                        type="date"
                                        name="birth"
                                        value="{{ isset($data["birth"]) ? $data['birth'] : '' }}"
                                        pattern="[0-9]{4}-[0-9]{2}-[0-9]{2}"
                                    />
                                </td>
                                <td>@lang("model.Tenant.confirm_by")</td>
                                <td>
                                    <select
                                        data-toggle="selectize"
                                        data-table="users"
                                        data-text="name"
                                        data-selected="{{ isset($data["confirm_by"]) ? $data['confirm_by'] : '0' }}"
                                        name="confirm_by"
                                        class="form-control form-control-sm"
                                    >
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td>@lang("model.Tenant.confirm_at")</td>
                                <td>
                                    <input
                                        class="form-control form-control-sm"
                                        type="date"
                                        name="confirm_at"
                                        value="{{ isset($data["confirm_at"]) ? $data['confirm_at'] : '' }}"
                                        pattern="[0-9]{4}-[0-9]{2}-[0-9]{2}"
                                    />
                                </td>
                            </tr>
                            </tbody>
                        </table>
                        <h3 class="mt-3">聯絡資料</h3>
                        @include('tenants.contact_info_form', ['prefix' => 'contact_infos', 'contact_infos' => $data['contact_infos']])

                        <h3 class="mt-3">緊急聯絡人</h3>
                        @include('tenants.related_people_form', ['prefix' => 'emergency_contact', 'relatedPeople' => $data['emergency_contacts']])

                        <h3 class="mt-3">保證人</h3>
                        @include('tenants.related_people_form', ['prefix' => 'guarantor', 'relatedPeople' => $data['guarantors']])

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
                certificate_number: {
                    required: true
                },
                line_id: {
                    required: true
                },
                residence_address: {
                    required: true
                },
                company: {
                    required: true,
                },
                job_position: {
                    required: true,
                },
                company_address: {
                    required: true
                },
                birth: {
                    required: true
                },
                confirm_by: {
                    required: true
                },
                confirm_at: {
                    required: true
                },
            };

            const messages = {
                name: {
                    required: '必須輸入'
                },
                certificate_number: {
                    required: '必須輸入'
                },
                line_id: {
                    required: '必須輸入'
                },
                residence_address: {
                    required: '必須輸入'
                },
                company: {
                    required: '必須輸入',
                },
                job_position: {
                    required: '必須輸入',
                },
                company_address: {
                    required: '必須輸入'
                },
                birth: {
                    required: '必須輸入'
                },
                confirm_by: {
                    required: '必須輸入'
                },
                confirm_at: {
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
