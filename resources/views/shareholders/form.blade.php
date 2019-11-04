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
                        @if( !isset($data["building_code"]) )
                            物件代碼 <input type="text" id="building_code"/>   
                            <button id="get_share_holders" type="button" class="btn btn-outline-info">
                                送出
                            </button>  
                            <span>請選擇: </span>
                            <select id="share_holders"></select> 
                        @endif                                       
                        <table class="table table-bordered">
                            <tbody>                            
                            <tr>
                                <td>@lang("model.Shareholder.building_code")</td>
                                <td colspan="3">
                                    <input
                                        class="form-control form-control-sm"
                                        name="building_code"
                                        value="{{ isset($data["building_code"]) ? $data['building_code'] : '' }}"
                                        placeholder="同時綁定多物件，格式為 777001,777002"
                                        @if ( isset($data["building_code"]) )
                                            readonly
                                        @endif
                                    />
                                </td>
                            </tr>
                            <tr>
                                <td>@lang("model.Shareholder.name")</td>
                                <td>
                                    <input
                                        class="form-control form-control-sm"
                                        type="text"
                                        name="name"
                                        value="{{ isset($data["name"]) ? $data['name'] : '' }}"
                                    />
                                </td>
                                <td>@lang("model.Shareholder.contact_method")</td>
                                <td>
                                    <input
                                        class="form-control form-control-sm"
                                        type="text"
                                        name="contact_method"
                                        value="{{ isset($data["contact_method"]) ? $data['contact_method'] : '' }}"
                                    />
                                </td>
                            </tr>
                            <tr>
                                <td>@lang("model.Shareholder.is_remittance_fee_collected")</td>
                                <td>
                                    {{-- unchecked value for checkbox--}}
                                    <input type="hidden" value="0" name="is_remittance_fee_collected"/>
                                    <input
                                        type="checkbox"
                                        name="is_remittance_fee_collected"
                                        value="1"
                                        {{ isset($data["is_remittance_fee_collected"]) ? ($data['is_remittance_fee_collected'] ? 'checked' : '') : '' }}
                                    />
                                </td>
                                <td>@lang("model.Shareholder.exchange_fee")</td>
                                <td>
                                    <input
                                        class="form-control form-control-sm is_remittance_fee_collected
                                            {{ isset($data["is_remittance_fee_collected"]) ? ($data['is_remittance_fee_collected'] ? '' : 'd-none') : 'd-none' }}"
                                        type="number"
                                        name="exchange_fee"
                                        min="1"
                                        value="{{ isset($data["exchange_fee"]) ? $data['exchange_fee'] : '' }}"
                                    />
                                </td>
                            </tr>
                            <tr>
                                <td>@lang("model.Shareholder.bank_name")</td>
                                <td>
                                    <input
                                        class="form-control form-control-sm"
                                        type="text"
                                        name="bank_name"
                                        value="{{ isset($data["bank_name"]) ? $data['bank_name'] : '' }}"
                                    />
                                </td>
                                <td>@lang("model.Shareholder.bank_branch")</td>
                                <td>
                                    <input
                                        class="form-control form-control-sm"
                                        type="text"
                                        name="bank_branch"
                                        value="{{ isset($data["bank_branch"]) ? $data['bank_branch'] : '' }}"
                                    />
                                </td>
                            </tr>

                            <tr>
                                <td>@lang("model.Shareholder.bank_code")</td>
                                <td>
                                    <input
                                        class="form-control form-control-sm"
                                        type="text"
                                        name="bank_code"
                                        value="{{ isset($data["bank_code"]) ? $data['bank_code'] : '' }}"
                                    />
                                </td>
                                <td>@lang("model.Shareholder.account_number")</td>
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
                                <td>@lang("model.Shareholder.account_name")</td>
                                <td>
                                    <input
                                        class="form-control form-control-sm"
                                        type="text"
                                        name="account_name"
                                        value="{{ isset($data["account_name"]) ? $data['account_name'] : '' }}"
                                    />
                                </td>
                                <td>@lang("model.Shareholder.transfer_from")</td>
                                <td>
                                    <select
                                        class="form-control form-control-sm"
                                        name="transfer_from"
                                        value="{{ $data['transfer_from'] ?? '' }}"
                                    />
                                        @foreach(config('enums.shareholders.transfer_from') as $value)
                                            <option value="{{$value}}">{{$value}}</option>
                                        @endforeach
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td>@lang("model.Shareholder.bill_delivery")</td>
                                <td>
                                    <input
                                        class="form-control form-control-sm"
                                        type="text"
                                        name="bill_delivery"
                                        value="{{ isset($data["bill_delivery"]) ? $data['bill_delivery'] : '' }}"
                                    />
                                </td>
                                <td>@lang("model.Shareholder.distribution_method")</td>
                                <td>
                                    <select
                                        class="form-control form-control-sm"
                                        name="distribution_method"
                                    >
                                        @foreach(config('enums.shareholders.distribution_method') as $value)
                                            <option value="{{$value}}" {{ ($data['distribution_method'] ?? '') === $value ? 'selected': '' }}>{{$value}}</option>
                                        @endforeach
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td>@lang("model.Shareholder.distribution_start_date")</td>
                                <td>
                                    <input
                                        class="form-control form-control-sm validate_distribution_start_date"
                                        type="date"
                                        name="distribution_start_date"
                                        value="{{ isset($data["distribution_start_date"]) ? $data['distribution_start_date'] : '' }}"
                                    />
                                </td>
                                <td>@lang("model.Shareholder.distribution_end_date")</td>
                                <td>
                                    <input
                                        class="form-control form-control-sm"
                                        type="date"
                                        name="distribution_end_date"
                                        value="{{ isset($data["distribution_end_date"]) ? $data['distribution_end_date'] : '' }}"
                                    />
                                </td>
                            </tr>
                            <tr>
                                <td>@lang("model.Shareholder.distribution_rate")</td>
                                <td>
                                    <input
                                        class="form-control form-control-sm"
                                        type="number"
                                        name="distribution_rate"
                                        value="{{ isset($data["distribution_rate"]) ? $data['distribution_rate'] : '' }}"
                                    />
                                </td>
                                <td>@lang("model.Shareholder.distribution_amount")</td>
                                <td>
                                    <input
                                        class="form-control form-control-sm"
                                        type="number"
                                        name="distribution_amount"
                                        value="{{ isset($data["distribution_amount"]) ? $data['distribution_amount'] : '' }}"
                                    />
                                </td>
                            </tr>
                            <tr>
                                <td>@lang("model.Shareholder.investment_amount")</td>
                                <td>
                                    <input
                                        class="form-control form-control-sm"
                                        type="number"
                                        name="investment_amount"
                                        value="{{ isset($data["investment_amount"]) ? $data['investment_amount'] : '' }}"
                                    />
                                </td>
                                <td>@lang("model.Shareholder.method")</td>
                                <td>
                                    <select
                                        data-toggle="selectize"
                                        data-table="shareholders"
                                        data-text="method"
                                        data-selected="{{ isset($data["method"]) ? $data['method'] : '0' }}"
                                        name="method"
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
    <script id="event">
        $('input[name=is_remittance_fee_collected]').change(function () {
            const $is_remittance_fee_collected = $('.is_remittance_fee_collected');
            $is_remittance_fee_collected.hasClass('d-none')
                ? $is_remittance_fee_collected.removeClass('d-none')
                : $is_remittance_fee_collected.addClass('d-none');
        });

        $('select[name=distribution_method]').change(function () {
            const value = $(this).find('option:checked').val();
            toggleRateAmount(value);
        })

        $('select#share_holders').change(function () {
            const $checkedOption = $(this).find('option:checked');
            fillInputs($checkedOption)
        });


        $('#get_share_holders').click(function () {
            const data = {
                building_code: $('#building_code').val() || '',
            }

            $.post('/api/shareHolders', data)
                .then(response => {
                    if (typeof response !== 'string') {
                        fillSelect(response);
                    } else {
                        alert('找不到對應的股東');
                    }
                });
        });

        function fillInputs($checkedOption) {
            const shareHolderInfo = $checkedOption.data();
            $('input[name=account_name]').val(shareHolderInfo.account_name || '')
            $('input[name=account_number]').val(shareHolderInfo.account_number || '')
            $('input[name=bank_code]').val(shareHolderInfo.bank_code || '')
            $('input[name=bank_name]').val(shareHolderInfo.bank_name || '')
            $('input[name=contact_method]').val(shareHolderInfo.contact_method || '')
            $('input[name=name]').val(shareHolderInfo.name || '')
            $('input[name=bank_branch]').val(shareHolderInfo.bank_branch || '')
        }

        function fillSelect(shareHolders) {
            if (shareHolders.length === 0) {
                return;
            }

            $('#share_holders').html('');
            Object.values(shareHolders).map((shareHolder, index) => {
                // 姓名、contact_method、銀行名稱、銀行代碼、銀行帳號、銀行戶名
                const $share_holders = $('#share_holders');
                if (index === 0) {
                    $share_holders.append('<option value="">請選擇</option>');
                }

                const option = `<option value="${shareHolder.id}"
                                        data-name="${shareHolder.name}"
                                        data-contact_method="${shareHolder.contact_method}"
                                        data-bank_name="${shareHolder.bank_name}"
                                        data-bank_code="${shareHolder.bank_code}"
                                        data-account_number="${shareHolder.account_number}"
                                        data-account_name="${shareHolder.account_name}"
                                        data-bank_branch="${shareHolder.bank_branch}"
                                >
                                    ${shareHolder.name}
                                </option>`;
                $share_holders.append(option);
            });

        }

        function toggleRateAmount(selectedText) {
            if (selectedText === '浮動') {
                $('input[name=distribution_rate]').removeClass('d-none');
                $('input[name=distribution_amount]').addClass('d-none');
            } else if (selectedText === '固定') {
                $('input[name=distribution_rate]').addClass('d-none');
                $('input[name=distribution_amount]').removeClass('d-none');
            }
        }
    </script>
    <script id="init">
        toggleRateAmount($('select[name=distribution_method]').val());
    </script>
    <script id="validation">

        $(document).ready(function () {
            $.validator.addMethod("validate_distribution_start_date", function(distribution_start_date, element) {
                const distribution_end_date =  $('input[name=distribution_end_date]').val()
                if (distribution_start_date.length !== 0 && distribution_end_date.length === 0 ) {
                    return false;
                }
                if (distribution_start_date > distribution_end_date) {
                    return false;
                }
                return true;
            }, "分配起需小於分配迄");

            const rules = {
                name: {
                    required: true
                },
                email: {
                    required: true
                },
                bank_name: {
                    required: true
                },
                bank_code: {
                    required: true,
                    maxlength: 7,
                    minlength: 7,
                },
                account_number: {
                    digits: true,
                    required: true,
                },
                account_name: {
                    required: true,
                },
                bill_delivery: {
                    required: true
                },
                distribution_start_date: {
                    required: true,
                },
                distribution_end_date: {
                    required: true
                },
                distribution_rate: {
                    required: true
                },
                investment_amount: {
                    required: true
                },
                method: {
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
                bank_name: {
                    required: '必須輸入'
                },
                bank_code: {
                    required: '必須輸入'
                },
                account_number: {
                    digits: '必須為數字',
                    required: '必須輸入',
                },
                account_name: {
                    required: '必須輸入',
                },
                bill_delivery: {
                    required: '必須輸入'
                },
                distribution_start_date: {
                    required: '必須輸入',
                },
                distribution_end_date: {
                    required: '必須輸入'
                },
                distribution_rate: {
                    required: '必須輸入'
                },
                investment_amount: {
                    required: '必須輸入'
                },
                method: {
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
