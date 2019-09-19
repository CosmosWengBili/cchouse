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
                    <form action="{{$action}}" method="POST" enctype="multipart/form-data">
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
                                <tr>
                                    <td>@lang("model.Landlord.bank_code")</td>
                                    <td>
                                        <input
                                            class="form-control form-control-sm"
                                            type="text"
                                            name="bank_code"
                                            value="{{ isset($data["bank_code"]) ? $data['bank_code'] : '' }}"
                                            maxlength="3"
                                            pattern="\d*"
                                            placeholder="例如新光銀行請輸入: 103"
                                        />
                                    </td>
                                    <td>@lang("model.Landlord.branch_code")</td>
                                    <td>
                                        <input
                                            class="form-control form-control-sm"
                                            type="text"
                                            name="branch_code"
                                            value="{{ isset($data["branch_code"]) ? $data['branch_code'] : '' }}"
                                            placeholder="分行名稱"
                                        />
                                    </td>
                                </tr>
                                <tr>
                                    <td>@lang("model.Landlord.account_name")</td>
                                    <td>
                                        <input
                                            class="form-control form-control-sm"
                                            type="text"
                                            name="account_name"
                                            value="{{ isset($data["account_name"]) ? $data['account_name'] : '' }}"
                                        />
                                    </td>
                                    <td>@lang("model.Landlord.account_number")</td>
                                    <td>
                                        <input
                                            class="form-control form-control-sm"
                                            type="text"
                                            name="account_number"
                                            value="{{ isset($data["account_number"]) ? $data['account_number'] : '' }}"
                                        />
                                    </td>
                                </tr>
                                <tr>
                                    <td>@lang("model.Landlord.invoice_collection_method")</td>
                                    <td>
                                        <select
                                            id="invoice_collection_method"
                                            class="form-control form-control-sm"
                                            name="invoice_collection_method"
                                            value="{{ isset($data["invoice_collection_method"]) ? $data['invoice_collection_method'] : '' }}"
                                        />
                                            @foreach(config('enums.landlord_contracts.invoice_collection_method') as $value)
                                                <option value="{{$value}}">{{$value}}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>@lang("model.Landlord.invoice_mailing_address")</td>
                                    <td>
                                        <input
                                            class="form-control form-control-sm"
                                            type="text"
                                            name="invoice_mailing_address"
                                            value="{{ isset($data["invoice_mailing_address"]) ? $data['invoice_mailing_address'] : '' }}"
                                        />
                                    </td>
                                </tr>
                                <tr>
                                    <td>@lang("model.Landlord.invoice_collection_number")</td>
                                    <td>
                                        <input
                                            id="invoice_collection_number"
                                            class="form-control form-control-sm"
                                            type="text"
                                            name="invoice_collection_number"
                                            value="{{ isset($data["invoice_collection_number"]) ? $data['invoice_collection_number'] : '' }}"
                                        />
                                    </td>
                                    <td>@lang("model.Landlord.landlord_contracts")</td>
                                    <td>
                                        <input
                                            class="form-control form-control-sm"
                                            type="text"
                                            id="landlord_contract_id"
                                        />
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                        <h3 class="mt-3">第三方代理文件</h3>
                        @include('documents.inputs', ['documentType' => 'third_party_file', 'documents' => $data['third_party_files']])

                        <h3 class="mt-3">聯絡資料</h3>
                        @include('landlords.contact_info_form', ['prefix' => 'contact_infos', 'contact_infos' => $data['contact_infos']])

                        <h3 class="mt-3">代理人</h3>
                        @include('landlords.agent_form', ['prefix' => 'agents', 'agents' => $data['agents']])


                        <button class="mt-5 btn btn-success" type="submit">送出</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script>

    const queryStringName = 'landlord_contract_id';
    const inputId = 'landlord_contract_id';

    const qs = window.myQueryString();
    qs.setInputValue(queryStringName, inputId);
    $('#' + inputId).addClass('d-none');


    const toggleElement = {
        selectElement: null,
        toggledElement: null,
        bind: function (selectElement, toggledElement) {
            this.selectElement = selectElement;
            this.toggledElement = toggledElement;

            document.getElementById(this.selectElement.id)
                .addEventListener('change', () => this.display())

            return this
        },
        display: function () {
            const option1 = '寄送';
            const option2 = '載具';
            const selectedValue = this.selectElement.value;

            if (selectedValue === option1) {
                this.toggledElement.classList.add('d-none');
            } else if (selectedValue === option2) {
                this.toggledElement.classList.remove('d-none');
            }

            return this
        }
    };

    $(document).ready(function () {
        toggleElement.bind(
            document.getElementById('invoice_collection_method'),
            document.getElementById('invoice_collection_number')
        ).display()
    })

</script>
<script id="validation">

    $(document).ready(function () {

        const rules = {
            name: {
                required: true,
            },
            certificate_number: {
                required: true,
            },
            birth: {
                dateISO: true,
            },
            bank_code: {
                digits: true,
                minlength: 3,
                maxlength: 3,
            },
            account_number: {
                digits: true
            },
            invoice_mailing_address: {
                email: true
            },
            invoice_collection_number: {
                digits: true
            },
        };

        const messages = {
            name: {
                required: '必須輸入'
            },
            certificate_number: {
                required: '必須輸入'
            },
            bank_code: {
                minlength: "只能輸入 {0} 個數字",
                maxlength: "只能輸入 {0} 個數字",
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
