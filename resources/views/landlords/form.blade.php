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

                        <h3 class="mt-3">基本資料</h3>
                        <table class="table table-bordered">
                            <tbody>
                                <tr>
                                    <td>@lang("model.Landlord.name")</td>
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
                                    <td>@lang("model.Landlord.certificate_number")</td>
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
                                    <td>@lang("model.Landlord.birth")</td>
                                    <td>
                                        <input
                                            class="form-control form-control-sm"
                                            type="date"
                                            name="birth"
                                            value="{{ isset($data["birth"]) ? $data['birth'] : '' }}"
                                        />
                                    </td>
                                </tr>
                                <tr>
                                    <td>@lang("model.Landlord.note")</td>
                                    <td>
                                        <input
                                            class="form-control form-control-sm"
                                            type="text"
                                            name="note"
                                            value="{{ isset($data["note"]) ? $data['note'] : '' }}"
                                        />
                                    </td>
                                </tr>
                                <tr>
                                    <td>@lang("model.Landlord.is_legal_person")</td>
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
                                </tr>
                                <tr>
                                    <td>@lang("model.Landlord.is_collected_by_third_party")</td>
                                    <td>
                                        {{-- unchecked value for checkbox--}}
                                        <input type="hidden" value="0" name="is_collected_by_third_party"/>
                                        <input
                                            type="checkbox"
                                            name="is_collected_by_third_party"
                                            value="1"
                                            {{ isset($data["is_collected_by_third_party"]) ? ($data['is_collected_by_third_party'] ? 'checked' : '') : '' }}
                                        />
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                        <h3 class="mt-3">聯絡資料</h3>
                        @include('Landlords.contact_info_form', ['prefix' => 'contact_infos', 'contact_infos' => $data['contact_infos']])

                        <h3 class="mt-3">代理人</h3>
                        @include('Landlords.agent_form', ['prefix' => 'agents', 'agents' => $data['agents']])


                        <button class="mt-5 btn btn-success" type="submit">送出</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
