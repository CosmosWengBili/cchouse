@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8 mt-5">
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
                                <td>@lang("model.Tenant.name")</td>
                                <td>
                                    <input
                                        class="form-control form-control-sm"
                                        type="text"
                                        name="name"
                                        value="{{ isset($data["name"]) ? $data['name'] : '' }}"
                                    />
                                </td>
                            </tr>

                            <tr>
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
                                    <input
                                        type="checkbox"
                                        name="is_legal_person"
                                        value="1"
                                        {{ isset($data["is_legal_person"]) ? ($data['is_legal_person'] ? 'checked' : '') : '' }}
                                    />
                                </td>
                            </tr>

                            <tr>
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
                            </tr>

                            <tr>
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
                            </tr>

                            <tr>
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
                            </tbody>
                        </table>

                        <button class="mt-5 btn btn-success" type="submit">送出</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
