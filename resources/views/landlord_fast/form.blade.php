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
                        <form id="myform" action="{{$action}}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method($method)

                            <div class="duplicated"></div>
                            <div class="d-flex justify-content-center">
                                <button class="mt-5 btn btn-inverse-info add-form" type="button">新增基本資料</button>
                            </div>

                            <div class="contract-duplicated"></div>
                            <div class="d-flex justify-content-center">
                                <button class="mt-5 btn btn-inverse-info add-contract-form" type="button">新增房東合約</button>
                            </div>

                            <div class="d-flex justify-content-center">
                                <button class="mt-5 btn btn-success" type="submit">送出</button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{--房東表單--}}
    <template id="basic_template">
        <div class="duplicate" data-form-index="0">
            <div class="d-flex justify-content-between">
                <h3 class="mt-3">基本資料</h3>
                <button type="button" class="btn btn-xs btn-danger align-self-center delete-basic">刪除</button>
            </div>
            <table class="table table-bordered">
                <tbody>
                <tr>
                    <td>@lang("model.Landlord.name")</td>
                    <td>
                        <input
                            class="form-control form-control-sm name"
                            type="text"
                            name="name[0]"
                            value="{{ isset($data["name"]) ? $data['name'] : '' }}"
                        />
                    </td>
                    <td>@lang("model.Landlord.certificate_number")</td>
                    <td>
                        <input
                            class="form-control form-control-sm certificate_number"
                            type="text"
                            name="certificate_number[0]"
                            value="{{ isset($data["certificate_number"]) ? $data['certificate_number'] : '' }}"
                        />
                    </td>
                </tr>
                <tr>
                    <td>@lang("model.Landlord.birth")</td>
                    <td>
                        <input
                            class="form-control form-control-sm birth"
                            type="date"
                            name="birth[0]"
                            value="{{ isset($data["birth"]) ? $data['birth'] : '' }}"
                        />
                    </td>
                    <td>@lang("model.Landlord.note")</td>
                    <td>
                        <input
                            class="form-control form-control-sm"
                            type="text"
                            name="note[0]"
                            value="{{ isset($data["note"]) ? $data['note'] : '' }}"
                        />
                    </td>
                </tr>
                <tr>
                    <td>@lang("model.Landlord.is_legal_person")</td>
                    <td>
                        {{-- unchecked value for checkbox--}}
                        <input type="hidden" value="0" name="is_legal_person[0]"/>
                        <input
                            type="checkbox"
                            name="is_legal_person[0]"
                            value="1"
                            {{ isset($data["is_legal_person"]) ? ($data['is_legal_person'] ? 'checked' : '') : '' }}
                        />
                    </td>
                    <td>@lang("model.Landlord.is_collected_by_third_party")</td>
                    <td>
                        {{-- unchecked value for checkbox--}}
                        <input type="hidden" value="0" name="is_collected_by_third_party_landlord[0]"/>
                        <input
                            type="checkbox"
                            name="is_collected_by_third_party_landlord[0]"
                            value="1"
                            {{ isset($data["is_collected_by_third_party"]) ? ($data['is_collected_by_third_party'] ? 'checked' : '') : '' }}
                        />
                    </td>
                </tr>
                <tr>
                    <td>@lang("model.Landlord.bank_code")</td>
                    <td>
                        <input
                            class="form-control form-control-sm bank_code"
                            type="text"
                            name="bank_code[0]"
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
                            name="branch_code[0]"
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
                            name="account_name[0]"
                            value="{{ isset($data["account_name"]) ? $data['account_name'] : '' }}"
                        />
                    </td>
                    <td>@lang("model.Landlord.account_number")</td>
                    <td>
                        <input
                            class="form-control form-control-sm account_number"
                            type="text"
                            name="account_number[0]"
                            value="{{ isset($data["account_number"]) ? $data['account_number'] : '' }}"
                        />
                    </td>
                </tr>
                <tr>
                    <td>@lang("model.Landlord.invoice_collection_method")</td>
                    <td>
                        <select
                            class="form-control form-control-sm invoice_collection_method"
                            name="invoice_collection_method[0]"
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
                            class="form-control form-control-sm invoice_mailing_address"
                            type="text"
                            name="invoice_mailing_address[0]"
                            value="{{ isset($data["invoice_mailing_address"]) ? $data['invoice_mailing_address'] : '' }}"
                        />
                    </td>
                </tr>
                <tr>
                    <td>@lang("model.Landlord.invoice_collection_number")</td>
                    <td>
                        <input
                            id="invoice_collection_number"
                            class="form-control form-control-sm invoice_collection_number"
                            type="text"
                            name="invoice_collection_number[0]"
                            value="{{ isset($data["invoice_collection_number"]) ? $data['invoice_collection_number'] : '' }}"
                        />
                    </td>
                </tr>
                </tbody>
            </table>
            <div class="row">
                <div class="col-12 col-md-3">
                    <h3 class="mt-3 text-center">第三方代理文件</h3>
                    @include('documents.multiinputs', ['documentType' => 'third_party_file', 'documents' => $data['third_party_files']])
                </div>
                <div class="col-12 col-md-3">
                    <h3 class="mt-3 text-center">聯絡資料</h3>
                    @include('landlord_fast.contact_info_form', ['prefix' => 'contact_infos', 'contact_infos' => $data['contact_infos']])
                </div>
                <div class="col-12 col-md-6">
                    <h3 class="mt-3 text-center">代理人</h3>
                    @include('landlord_fast.agent_form', ['prefix' => 'agents', 'agents' => $data['agents']])
                </div>
            </div>
        </div>
    </template>
    {{--房東合約表單--}}
    <template id="contract_template">
        <div class="duplicate" data-form-index="0">
            <div class="d-flex justify-content-between">
                <h3 class="mt-3">基本資料</h3>
                <button type="button" class="btn btn-xs btn-outline-danger align-self-center delete-basic">刪除</button>
            </div>
            <table class="table table-bordered">
                <tbody>
                <tr>
                    <td>@lang("model.LandlordContract.building_id")</td>
                    <td>
                        <select
                            data-toggle="selectize"
                            data-table="buildings"
                            data-text="address"
                            data-selected="{{ isset($data["building_id"]) ? $data['building_id'] : '0' }}"
                            name="building_id[0]"
                            class="form-control form-control-sm"
                        >
                        </select>
                    </td>
                    <td>@lang("model.LandlordContract.commission_type")</td>
                    <td>
                        <select
                            class="form-control form-control-sm"
                            name="commission_type[0]"
                            value="{{ isset($data["commission_type"]) ? $data['commission_type'] : '' }}"
                        />
                        @foreach(config('enums.landlord_contracts.commission_type') as $value)
                            <option value="{{$value}}">{{$value}}</option>
                            @endforeach
                            </select>
                    </td>
                </tr>

                <tr>
                    <td>@lang("model.LandlordContract.commission_start_date")</td>
                    <td>
                        <input
                            class="form-control form-control-sm"
                            type="date"
                            name="commission_start_date[0]"
                            value="{{ isset($data["commission_start_date"]) ? $data['commission_start_date'] : '' }}"
                        />
                    </td>
                    <td>@lang("model.LandlordContract.commission_end_date")</td>
                    <td>
                        <input
                            class="form-control form-control-sm"
                            type="date"
                            name="commission_end_date[0]"
                            value="{{ isset($data["commission_end_date"]) ? $data['commission_end_date'] : '' }}"
                        />
                    </td>
                </tr>
                <tr>
                    <td>@lang("model.LandlordContract.warranty_start_date")</td>
                    <td>
                        <input
                            class="form-control form-control-sm warranty_start_date"
                            type="date"
                            name="warranty_start_date[0]"
                            value="{{ isset($data["warranty_start_date"]) ? $data['warranty_start_date'] : '' }}"
                        />
                    </td>
                    <td>@lang("model.LandlordContract.warranty_end_date")</td>
                    <td>
                        <input
                            class="form-control form-control-sm warranty_end_date"
                            type="date"
                            name="warranty_end_date[0]"
                            value="{{ isset($data["warranty_end_date"]) ? $data['warranty_end_date'] : '' }}"
                        />
                    </td>
                </tr>
                <tr>
                    <td>@lang("model.LandlordContract.rental_decoration_free_start_date")</td>
                    <td>
                        <input
                            class="form-control form-control-sm rental_decoration_free_start_date"
                            type="date"
                            name="rental_decoration_free_start_date[0]"
                            value="{{ isset($data["rental_decoration_free_start_date"]) ? $data['rental_decoration_free_start_date'] : '' }}"
                        />
                    </td>
                    <td>@lang("model.LandlordContract.rental_decoration_free_end_date")</td>
                    <td>
                        <input
                            class="form-control form-control-sm rental_decoration_free_end_date"
                            type="date"
                            name="rental_decoration_free_end_date[0]"
                            value="{{ isset($data["rental_decoration_free_end_date"]) ? $data['rental_decoration_free_end_date'] : '' }}"
                        />
                    </td>
                </tr>
                <tr>
                    <td>@lang("model.LandlordContract.annual_service_fee_month_count")</td>
                    <td>
                        <input
                            class="form-control form-control-sm"
                            type="text"
                            name="annual_service_fee_month_count[0]"
                            value="{{ isset($data["annual_service_fee_month_count"]) ? $data['annual_service_fee_month_count'] : '' }}"
                        />
                    </td>
                    <td>@lang("model.LandlordContract.charter_fee")</td>
                    <td>
                        <input
                            class="form-control form-control-sm charter_fee"
                            type="text"
                            name="charter_fee[0]"
                            value="{{ isset($data["charter_fee"]) ? $data['charter_fee'] : '' }}"
                        />
                    </td>
                </tr>

                <tr>
                    <td>@lang("model.LandlordContract.taxable_charter_fee")</td>
                    <td>
                        <input
                            class="form-control form-control-sm taxable_charter_fee"
                            type="text"
                            name="taxable_charter_fee[0]"
                            value="{{ isset($data["taxable_charter_fee"]) ? $data['taxable_charter_fee'] : '' }}"
                        />
                    </td>
                    <td>@lang("model.LandlordContract.agency_service_fee")</td>
                    <td>
                        <input
                            class="form-control form-control-sm agency_service_fee"
                            type="text"
                            name="agency_service_fee[0]"
                            value="{{ isset($data["agency_service_fee"]) ? $data['agency_service_fee'] : '' }}"
                        />
                    </td>
                </tr>
                <tr>
                    <td>@lang("model.LandlordContract.rent_collection_frequency")</td>
                    <td>
                        <select
                            class="form-control form-control-sm"
                            name="rent_collection_frequency[0]"
                            value="{{ isset($data["rent_collection_frequency"]) ? $data['rent_collection_frequency'] : '' }}"
                        />
                        @foreach(config('enums.landlord_contracts.rent_collection_frequency') as $value)
                            <option value="{{$value}}">{{$value}}</option>
                            @endforeach
                            </select>
                    </td>
                    <td>@lang("model.LandlordContract.rent_collection_time")</td>
                    <td>
                        <input
                            class="form-control form-control-sm rent_collection_time"
                            type="text"
                            name="rent_collection_time[0]"
                            value="{{ isset($data["rent_collection_time"]) ? $data['rent_collection_time'] : '' }}"
                        />
                    </td>
                </tr>
                <tr>
                    <td>@lang("model.LandlordContract.rent_adjusted_date")</td>
                    <td>
                        <input
                            class="form-control form-control-sm rent_adjusted_date"
                            type="date"
                            name="rent_adjusted_date[0]"
                            value="{{ isset($data["rent_adjusted_date"]) ? $data['rent_adjusted_date'] : '' }}"
                        />
                    </td>
                    <td>@lang("model.LandlordContract.adjust_ratio")</td>
                    <td>
                        <input
                            class="form-control form-control-sm adjust_ratio"
                            type="text"
                            name="adjust_ratio[0]"
                            value="{{ isset($data["adjust_ratio"]) ? $data['adjust_ratio'] : '' }}"
                        />
                    </td>
                </tr>
                <tr>
                    <td>@lang("model.LandlordContract.deposit_month_count")</td>
                    <td>
                        <input
                            class="form-control form-control-sm deposit_month_count"
                            type="text"
                            name="deposit_month_count[0]"
                            value="{{ isset($data["deposit_month_count"]) ? $data['deposit_month_count'] : '' }}"
                        />
                    </td>
                    <td>@lang("model.LandlordContract.is_collected_by_third_party")</td>
                    <td>
                        <input type="hidden" value="0" name="is_collected_by_third_party_contract[0]"/>
                        <input
                            type="checkbox"
                            name="is_collected_by_third_party_contract[0]"
                            value="1"
                            {{ isset($data["is_collected_by_third_party"]) ? ($data['is_collected_by_third_party'] ? 'checked' : '') : '' }}
                        />
                    </td>
                </tr>
                <tr>
                    <td>@lang("model.LandlordContract.is_notarized")</td>
                    <td>
                        @foreach(config('enums.landlord_contracts.is_notarized') as $notarizedText)
                            <label class="form-check-label">
                                <input
                                    type="radio"
                                    name="is_notarized[0]"
                                    value="{{ $notarizedText }}"
                                    {{ $loop->first || (isset($data['is_notarized']) && ($data['is_notarized'] == $notarizedText)) ? 'checked': '' }}
                                />
                                {{ $notarizedText }}
                            </label>
                        @endforeach
                    </td>
                    <td>@lang("model.LandlordContract.commissioner_id")</td>
                    <td>
                        <select
                            data-toggle="selectize"
                            data-table="users"
                            data-text="name"
                            data-selected="{{ isset($data["commissioner_id"]) ? $data['commissioner_id'] : '0' }}"
                            name="commissioner_id[0]"
                            class="form-control form-control-sm"
                        >
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>@lang("model.LandlordContract.can_keep_pets")</td>
                    <td>

                        <input type="hidden" value="0" name="can_keep_pets[0]"/>
                        <input
                            type="checkbox"
                            name="can_keep_pets[0]"
                            value="1"
                            {{ isset($data["can_keep_pets"]) ? ($data['can_keep_pets'] ? 'checked' : '') : '' }}
                        />
                    </td>
                    <td>@lang("model.LandlordContract.gender_limit")</td>
                    <td>
                        <select
                            class="form-control form-control-sm"
                            name="gender_limit[0]"
                            value="{{ $data['gender_limit'] ?? '' }}"
                        />
                        @foreach(config('enums.landlord_contracts.gender_limit') as $value)
                            <option value="{{$value}}">{{$value}}</option>
                            @endforeach
                            </select>
                    </td>
                </tr>
                </tbody>
            </table>

            <h3 class="mt-3">合約原檔</h3>
            @include('documents.multiinputs', ['documentType' => 'original_file', 'documents' => $data['original_files']])
        </div>
    </template>

    <script id="init">
        $(document).ready(function () {
            cloneBasic.clone()
            cloneContract.clone()

            $(document).on('click', 'button.delete-basic', function () {
                if ($(this).parents('div.duplicate').attr('data-form-index') == '0') {
                    alert('至少需要有一張表單')
                    return;
                }

                $(this).parents('div.duplicate').slideUp(400, () => { $(this).remove() })
            })

            $(document).on('change', 'select.invoice_collection_method', function () {
                const option1 = '寄送';
                const option2 = '載具';
                const selectedValue = $(this).val();

                let $toggledElement = $(this).parents('tbody').find('.invoice_collection_number')

                if (selectedValue === option1) {
                    $toggledElement.addClass('d-none');
                } else if (selectedValue === option2) {
                    $toggledElement.removeClass('d-none')
                }
            });


            $select = $('.landlord_ids').selectize({
                delimiter: ',',
                persist: false,
                create: function(input) {
                    return {
                        value: input,
                        text: input
                    }
                }
            });
        })
    </script>

    <script id="add-basic-form">
        $('button.add-form').click(function () {
            cloneBasic.clone()
        })

        const cloneBasic = {
            index: 0,
            emptyObj: $($('#basic_template').html().trim()),
            clone: function () {
                const selfIndex = this.index
                const clone = this.emptyObj.clone()

                clone.attr('data-form-index', selfIndex)
                clone.find("input[name*='[0]'], select[name*='[0]']").each(function (key, item) {
                    let inputName = $(item).attr('name');
                    let newName = inputName.replace('[0]', '[' + selfIndex + ']');
                    $(item).attr('name', newName)
                    $(item).rules('add', 'required')
                })
                $('div.duplicated').append(clone)
                this.index++;
            }
        }

    </script>

    <script id="add-contract-form">
        $('button.add-contract-form').click(function () {
            cloneContract.clone()
        })

        const cloneContract = {
            index: 0,
            emptyObj: $($('#contract_template').html().trim()),
            clone: function () {
                const selfIndex = this.index
                const clone = this.emptyObj.clone()
                clone.attr('data-form-index', selfIndex)
                clone.find("input[name*='[0]'], select[name*='[0]']").each(function (key, item) {
                    let inputName = $(item).attr('name');
                    let newName = inputName.replace('[0]', '[' + selfIndex + ']');
                    $(item).attr('name', newName)
                    $(item).rules('add', 'required')
                })

                // bind select element which is dynamic generated
                window.realtimeSelect(clone.find('[data-toggle=selectize]'))

                $('div.contract-duplicated').append(clone)
                this.index++;
            }
        }
    </script>

    <script id="event_in_form">

        $(document).on('change', 'input.adjust_ratio', function(){
            const $charter_fee = $(this).parents('tbody').find('input.charter_fee');
            const ratio = Number.parseFloat($(this).val()) || 0;
            const charter_fee   = Number.parseFloat($charter_fee.val()) || 0;
            if (_.lte(ratio, 100)) {
                // 用 % 數調漲
                const result = getChangedCharterFee(charter_fee, ratio)
                $charter_fee.val(result)
            } else {
                // 直接將租金加上此值
                $charter_fee.val(_.add(ratio, charter_fee))
            }
        });

        // business logic
        function getChangedCharterFee(charter_fee, ratio) {
            const d_percent = _.divide(ratio, 100)
            const multiply = _.multiply(charter_fee, d_percent)

            return _.add(charter_fee, multiply)
        }

    </script>

    <script id="validation">


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

        // basic form validation below
        $.validator.addClassRules('name', {
            required: true
        });
        $.validator.addClassRules('certificate_number', {
            required: true
        });
        $.validator.addClassRules('birth', {
            required: true,
        });
        $.validator.addClassRules('bank_code', {
            required: true,
            digits: true,
            minlength: 3,
            maxlength: 3,
        });
        $.validator.addClassRules('account_number', {
            required: true,
            digits: true
        });
        $.validator.addClassRules('invoice_mailing_address', {
            required: true,
            email: true
        });
        $.validator.addClassRules('invoice_collection_number', {
            required: true,
            digits: true
        });

        // contract form validation below
        $.validator.addClassRules('warranty_start_date', {
            required: true,
        });
        $.validator.addClassRules('warranty_end_date', {
            required: true,
        });
        $.validator.addClassRules('rental_decoration_free_start_date', {
            required: true,
        });
        $.validator.addClassRules('rental_decoration_free_end_date', {
            required: true,
        });
        $.validator.addClassRules('agency_service_fee', {
            required: true,
        });
        $.validator.addClassRules('charter_fee', {
            required: true,
        });
        $.validator.addClassRules('taxable_charter_fee', {
            required: true,
        });
        $.validator.addClassRules('rent_collection_time', {
            required: true,
        });
        $.validator.addClassRules('rent_adjusted_date', {
            required: true,
        });
        $.validator.addClassRules('adjust_ratio', {
            required: true,
        });
        $.validator.addClassRules('deposit_month_count', {
            required: true,
        });

        // error messages move to global.js, need to compile


        $('form').validate({
                // rules: rules,
                // messages: messages,
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




    </script>
@endsection
