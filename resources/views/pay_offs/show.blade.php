@extends('layouts.app')
@section('content')

<div class="container">
    <div class="justify-content-center">
        <div class="row my-3">
            <div class="col-12 card">
                <div class="card-body">
                    <div class="card-title"></div>

                    <form class="mt-3 mb-1" method="get">
                        <div class="form-group m-0">
                            <label style="vertical-align: text-top;" for="pay-off-date">產生點交報表：</label>
                            <input type="date" name="payOffDate" class="form-control-sm" id="pay-off-date" value="{{ optional($payOffDate)->format('Y-m-d') }}">
                            <button type="submit" class="btn btn-info btn-xs">送出</button>
                        </div>
                    </form>
                    <hr class="mt-1">


                    @if(isset($payOffData))
                        @php
                            $refundAmount = 0;
                        @endphp

                        <ul class="list-group mb-3">
                            <li class="list-group-item">
                                <div class="d-inline-flex flex-grow-1 font-weight-bolder">承租方式: </div>
                                <div id="commission_type" class="d-inline-flex">{{ $headerInfo['commission_type'] }}</div>
                            </li>
                            <li class="list-group-item">
                                <div class="d-inline-flex font-weight-bolder">退租方式: </div>
                                <div class="d-inline-flex">
                                    <select name="return_ways" id="return_ways">
                                        @foreach(config('enums.pay_logs.return_ways') as $return_way)
                                            <option value="{{ $return_way }}"
                                                {{request()->input('return_ways', '中途退租') === $return_way ? 'selected': ''}}
                                            >
                                                {{ $return_way }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </li>
                            <li class="list-group-item">
                                <div class="d-inline-flex font-weight-bolder">租客姓名: </div>
                                <div class="d-inline-flex">{{ $headerInfo['tenant_name'] }}</div>
                            </li>
                            <li class="list-group-item">
                                <div class="d-inline-flex font-weight-bolder">房代號: </div>
                                <div class="d-inline-flex">
                                    <a
                                        target="_blank"
                                        href="{{ route('rooms.show', $headerInfo['room_code']) }}"
                                    >
                                        {{ $headerInfo['room_code'] }}
                                    </a>
                                </div>
                            </li>
                            <li class="list-group-item">
                                <div class="d-inline-flex font-weight-bolder">地址: </div>
                                <div class="d-inline-flex">{{ $headerInfo['location'] }}</div>
                            </li>
                        </ul>

                        <table class="table table-bordered">
                            <tbody>
                            <!-- divide eletecity section to two method -->
                            @if( $tenantContract->room->building->electricity_payment_method == '儲值電表' )
                                <tr>
                                    <th>110v 電費度數</th>
                                    <td colspan="3">                          
                                        <div class="input-group w-50">
                                            <input
                                                class="form-control form-control-sm ml-3"
                                                type="number"
                                                id="e_110v_stored"
                                                name="e_110v_stored"
                                            />
                                            <div class="input-group-append">
                                                    <button
                                                        id="cal_110v_stored"
                                                        type="button"
                                                        class="btn btn-sm btn-outline-info"
                                                    >
                                                    <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                                    計算
                                                </button>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th>220v 電費度數</th>
                                    <td colspan="3">
                                        <div class="input-group w-50">
                                            <input
                                                class="form-control form-control-sm ml-3"
                                                type="number"
                                                id="e_220v_stored"
                                                name="e_220v_stored"
                                            />
                                        <div class="input-group-append">
                                            <button
                                                id="cal_220v_stored"
                                                type="button"
                                                class="btn btn-sm btn-outline-info"
                                            >
                                                <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                                計算
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @else
                                <tr>
                                    <th>110v 電費度數</th>
                                    <td colspan="3">
                                        <form id="e_110v_form" onsubmit="return false;">
                                            <div class="input-group w-50">
                                                <span class="align-self-center"><span class="old-110v">{{ $payOffData['110v_end_degree'] }}</span> 度</span>
                                                <input
                                                    class="form-control form-control-sm ml-3"
                                                    type="number"
                                                    id="e_110v"
                                                    name="e_110v"
                                                    value="{{ $payOffData['110v_end_degree'] }}"
                                                    min="{{ $payOffData['110v_end_degree'] }}"
                                                />
                                                <div class="input-group-append">
                                                    <button
                                                        id="cal_110v"
                                                        type="button"
                                                        class="btn btn-sm btn-outline-info"
                                                    >
                                                        <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                                        計算
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    </td>
                                </tr>
                                <tr>
                                    <th>220v 電費度數</th>
                                    <td colspan="3">
                                        <form id="e_220v_form" onsubmit="return false;">
                                            <div class="input-group w-50">
                                                <span class="align-self-center"><span class="old-220v">{{ $payOffData['220v_end_degree'] }}</span> 度</span>
                                                <input
                                                    class="form-control form-control-sm ml-3"
                                                    type="number"
                                                    id="e_220v"
                                                    name="e_220v"
                                                    value="{{ $payOffData['220v_end_degree'] }}"
                                                    min="{{ $payOffData['220v_end_degree'] }}"
                                                />
                                                <div class="input-group-append">
                                                    <button
                                                        id="cal_220v"
                                                        type="button"
                                                        class="btn btn-sm btn-outline-info"
                                                    >
                                                        <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                                        計算
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    </td>
                                </tr>
                            @endif
                            <tr class='report-header'>
                                <th>科目</th>
                                <th>費用</th>
                                <th>備註</th>
                                <th>負擔方</th>
                            </tr>
                            @foreach($payOffData['fees'] as $fee)
                                @continue($fee['is_showed'] === false || $fee['is_tenant'] === false)
                                <tr class="old-payment">
                                    <td><span class="subject">{{ $fee['subject'] }}</span></td>
                                    <td>
                                        @php
                                            $refundAmount += $fee['amount'];
                                        @endphp
                                        <input data-subject="{{$fee['subject']}}" class="form-control form-control-sm edit-new-item-amount" type="number" value={{ $fee['amount'] }}>
                                    </td>
                                    <td><span class="comment">{{ $fee['comment'] }}</span></td>
                                    <td></td>
                                </tr>
                            @endforeach
                            <tr class="functions-row">
                                <td colspan="4" class="text-center">
                                    <button class="btn btn-success btn-xs js-new-item">新增項目</button>
                                    <label for="exchange_fee">匯費</label>
                                    <input type="checkbox" id="exchange_fee" >
                                </td>
                            </tr>
                            <tr>
                                <th>應退房客金額</th>
                                <td colspan="3">
                                    <div class="d-inline-flex">
                                        <div class="align-content-center">
                                            <span id="refund_amount">{{ $payOffData['sums']['應退金額'] }}</span>
                                            <input id="edit_refund_amount"
                                                   data-subject="refund"
                                                   type="number"
                                                   step="1"
                                                   value="{{ $payOffData['sums']['應退金額'] }}"
                                                   class="d-none"
                                            >
                                            <span>元</span>
                                            <button id="edit_refund" class="btn btn-xs btn-primary">編輯</button>
                                            <button id="update_refund" class="btn btn-xs btn-info d-none">確認</button>
                                            <button id="reset_refund" class="btn btn-xs btn-secondary">恢復</button>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @foreach($payOffData['fees'] as $fee)
                                @continue($fee['is_showed'] == false || $fee['is_tenant'] == true)
                                <tr class="old-payment">
                                    <td><span class="subject">{{ $fee['subject'] }}</span></td>
                                    <td>
                                        @php
                                            $refundAmount += $fee['amount'];
                                        @endphp
                                        <input data-subject="{{$fee['subject']}}" class="form-control form-control-sm edit-new-item-amount" type="number" value={{ $fee['amount'] }}>
                                    </td>
                                    <td><span class="comment">{{ $fee['comment'] }}</span></td>
                                    <td></td>
                                </tr>
                            @endforeach
                            <tr>
                                <th>兆基應收</th>
                                <td colspan="3">
                                    <div class="d-inline-flex">
                                        <span id="received_amount">{{ $payOffData['sums']['兆基應收'] }}</span>
                                        <input id="edit_received_amount"
                                                data-subject="received"
                                                type="number"
                                                step="1"
                                                value="{{ $payOffData['sums']['兆基應收'] }}"
                                                class="d-none"
                                        >
                                        <span>元</span>
                                        <button id="edit_received" class="btn btn-xs btn-primary">編輯</button>
                                        <button id="update_received" class="btn btn-xs btn-info d-none">確認</button>
                                        <button id="reset_received" class="btn btn-xs btn-secondary">恢復</button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th>業主應付</th>
                                <td colspan="3">
                                    <div class="d-inline-flex">
                                        <span id="pay_amount">{{ $payOffData['sums']['業主應付'] }}</span>
                                        <input id="edit_pay_amount"
                                                data-subject="pay"
                                                type="number"
                                                step="1"
                                                value="{{ $payOffData['sums']['業主應付'] }}"
                                                class="d-none">
                                        <span>元</span>
                                        <button id="edit_pay" class="btn btn-xs btn-primary">編輯</button>
                                        <button id="update_pay" class="btn btn-xs btn-info d-none">確認</button>
                                        <button id="reset_pay" class="btn btn-xs btn-secondary">恢復</button>
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                        <div class="text-center">
                            <button class="d-inline-block my-3 mx-auto btn btn-lg btn-info js-save-payments">儲存</button>
                            <input type="checkbox" name="is_monthly_report" id="is_monthly_report"> 為月結單資料
                        </div>

                    @else
                        <h3 class="text-center my-5">請選擇上方日期選擇器產生報表</h3>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    tr > td:first-child,
    tr > td:nth-child(2),
    tr > td:nth-child(3) {
        width: 200px
    }
    .report-header th:not(:last-child){
        width:25%;
    }
</style>

<!-- init payOffData -->
<script>
    var payOffData = @json($payOffData);
</script>

<script>

    // For normal eletricity payment 
    $('#cal_110v, #cal_220v').click(function () {
        const res_110_check = validate_110v.element( "#e_110v" );
        const res_220_check = validate_220v.element( "#e_220v" );
        if (res_110_check && res_220_check) {
            const res_110 = parseInt($( "#e_110v" ).val());
            const res_220 = parseInt($( "#e_220v" ).val());
            calculateElectricityPrice($(this), res_110, res_220, '普通');
        }
    });

    // For 儲值電 eletricity payment 
    $('#cal_110v_stored, #cal_220v_stored').click(function () {
        const res_110 = parseInt($( "#e_110v_stored" ).val());
        const res_220 = parseInt($( "#e_220v_stored" ).val());
        if ( isNaN(res_110) || isNaN(res_220) ) {
            Swal.fire('請務必兩欄都填入值')
        }
        else{
            calculateElectricityPrice($(this),res_110, res_220, '儲值電');
        }
    });

    function calculateElectricityPrice($clickedButton, input_110v, input_220v, mode = '普通') {
        const tenantContractsId = '{{ $tenantContract->id }}';
        const e_110v_end = parseInt('{{ $payOffData['110v_end_degree'] }}');
        const e_220v_end = parseInt('{{ $payOffData['220v_end_degree'] }}');
        const template = getElectricityTemplate();

        $clickedButton.find('span').removeClass('d-none');
        $.get('/tenantContracts/' + tenantContractsId + '/electricityDegree')
            .then(function (data) {
                const pricePerDegree = data.pricePerDegree || 0;
                const pricePerDegreeSummer = data.pricePerDegreeSummer || 0;
                const readMonth = (new Date).getMonth() + 1;
                const ratio = [7, 8, 9, 10].includes(readMonth) ? pricePerDegreeSummer : pricePerDegree;
                let amount = 0
                if( mode == '普通' ){
                    amount = _.round (
                        _.multiply (
                            _.add (
                                _.subtract(input_110v, e_110v_end),
                                _.subtract(input_220v, e_220v_end)
                            ),
                            ratio
                        )
                    );
                }
                else{
                    amount = input_110v * ratio + input_220v * ratio
                }


                let $calV = $('table tr.cal-v');
                if ($calV.length === 0) {
                    $(template).insertBefore($functionsRow);
                } else {
                    $calV.remove();
                    $(template).insertBefore($functionsRow);
                }
                $('#cal_v').val(amount * -1);
                countTenantPayment();
            })
            .always(function () {
                $clickedButton.find('span').addClass('d-none');
            })
    }

    function getElectricityTemplate() {
        return `
            <tr class="cal-v">
                <td>電費</td>
                <td>
                    <input class="form-control form-control-sm electricity-amount edit-added-item-amount" type="number" id="cal_v" name="cal_v" readonly>
                </td>
                <td>
                    <input class="form-control form-control-sm electricity-comment" type="text" name="comment">
                </td>
                
                <td>
                    <select class="form-control form-control-sm" name="collected_by">
                        @foreach(config('enums.tenant_payments.collected_by') as $collected_by)
                            <option value="{{ $collected_by }}" {{ $collected_by==='房東' ? 'selected' : ''}}>{{ $collected_by }}</option>
                        @endforeach
                    </select>
                </td>
            </tr>`;
    }

</script>

<script>

    $('#return_ways').change(function () {
        const selectedText = $(this).val();
        const payOffDate = window.myQueryString().getQueryStrings()['payOffDate'];
        const url = new URL(location.href);
        location.href = url.protocol + '//' + url.host + url.pathname + `?payOffDate=${payOffDate}&return_ways=${selectedText}`;

    });

    $('#exchange_fee').change(function () {
        if ($(this).prop('checked')) {
            // add item
            const template = getFeeTemplate();
            $(template).insertBefore($functionsRow);

        } else {
            // remove item
            $('table tr.exchange-fee').remove();
        }

        // 重新計算應退房客金額
        countTenantPayment();
    });

    function getFeeTemplate() {
        return `
            <tr class="exchange-fee">
                <td>匯費</td>
                <td>
                    <input class="form-control form-control-sm exchange-fee-amount edit-added-item-amount" type="number" name="amount" readonly value="-30">
                </td>
                <td>
                    <input class="form-control form-control-sm exchange-fee-comment" type="text" name="comment">
                </td>
                <td>
                    <select class="form-control form-control-sm" name="collected_by">
                        @foreach(config('enums.tenant_payments.collected_by') as $collected_by)
                            <option value="{{ $collected_by }}" {{$loop->first ? 'selected' : ''}}>{{ $collected_by }}</option>
                        @endforeach
                    </select>
                </td>
            </tr>`;
    }

</script>

<script>

    /* sum data editor */
    var Editor = function(element){
        return {
            unchanged: $(`#edit_${element}_amount`).val(),
            $amount: $(`#${element}_amount`),
            $edit_amount: $(`#edit_${element}_amount`),
            $edit_button: $(`#edit_${element}`),
            $update_button: $(`#update_${element}`),
            toggle: function () {
                if (this.$edit_amount.hasClass('d-none')) {
                    this.$edit_amount.removeClass('d-none');
                    this.$amount.addClass('d-none');
                    this.$update_button.removeClass('d-none');
                    this.$edit_button.addClass('d-none');
                } else {
                    this.$edit_amount.addClass('d-none');
                    this.$amount.removeClass('d-none');
                    this.$update_button.addClass('d-none');
                    this.$edit_button.removeClass('d-none');
                }
            },
            update: function () {
                this.$amount.text(this.$edit_amount.val());
            },
            reset: function () {
                this.$edit_amount.val(this.unchanged);
                this.$amount.text(this.unchanged);
            },
            set: function (value) {
                this.$edit_amount.val(value);
                this.$amount.text(value);
            }
        }
    }

    const EditReceived = new Editor('received');
    const EditPay = new Editor('pay');
    const EditRefund = new Editor('refund');

    $('#edit_refund').click(function () {
        EditRefund.toggle();
    });
    $('#update_refund').click(function () {
        EditRefund.update();
        EditRefund.toggle();
    });
    $('#reset_refund').click(function () {
        EditRefund.reset();
    });
    $('#edit_received').click(function () {
        EditReceived.toggle();
    });
    $('#update_received').click(function () {
        EditReceived.update();
        EditReceived.toggle();
    });
    $('#reset_received').click(function () {
        EditReceived.reset();
    });
    $('#edit_pay').click(function () {
        EditPay.toggle();
    });
    $('#update_pay').click(function () {
        EditPay.update();
        EditPay.toggle();
    });
    $('#reset_pay').click(function () {
        EditPay.reset();
    });

    $(document).on('change', 'input.edit-new-item-amount', function () {
        // update payOffData
        if( payOffData['fees'][$(this).data('subject')] == undefined ){
            payOffData['fees'][$(this).data('subject')] = {
                'sbject': $(this).data('subject'),
                'amount': parseInt($(this).val()),
                'comment': "",
                'is_showed': true
            }
        }
        else{
            payOffData['fees'][$(this).data('subject')]['amount'] = parseInt($(this).val())
        }
        reCountSum();
    });

    /**
     * 計算 應退房客金額
     */
    function countTenantPayment() {
        const returnWay = $('#return_ways').val()
        const originalRefund = payOffData['sums']['應退金額'];
        const addedAmount = document.querySelectorAll('input.edit-added-item-amount');
        let sum = parseInt(originalRefund);
        if( returnWay != "中途退租" ){
            addedAmount.forEach(function (element, key) {
            sum = _.add(sum, parseInt(element.value) );
            });
        }
        EditRefund.set(sum);
    }
    /**
     * 重新顯示所有總額
     */
     function renderSum(){
        $('[data-subject="refund"]').val(payOffData['sums']['應退金額'] )
        $('#refund_amount').text(payOffData['sums']['應退金額']) 
        $('[data-subject="received"]').val(payOffData['sums']['兆基應收'] )
        $('#received_amount').text(payOffData['sums']['兆基應收']) 
        $('[data-subject="pay"]').val(payOffData['sums']['業主應付'] )
        $('#pay_amount').text(payOffData['sums']['業主應付'])   
     }
    /**
     * 重新計算所有總額
     */
    function reCountSum(){
        var commissionType = $('#commission_type').text()
        var returnWay = $('#return_ways').val()
        if( commissionType == '包租' ){
            if( returnWay == '中途退租' ){
                // -(履保金+管理費+清潔費+設備+滯納金)
                payOffData['fees']['沒收押金']['amount'] = (
                    payOffData['fees']['履保金']['amount'] +
                    payOffData['fees']['管理費']['amount'] +
                    payOffData['fees']['清潔費']['amount'] +
                    payOffData['fees']['滯納金']['amount']
                    ) * -1;
                

                // ( 沒收押金 * -1 * ( 1 - landlordContract - withdrawal_revenue_distribution ) )
                payOffData['fees']['點交中退盈餘分配']['amount'] = payOffData['fees']['沒收押金']['amount'] * -1 * (1 - payOffData['withdrawal_revenue_distribution']);
                payOffData['sums']['兆基應收'] = payOffData['fees']['履保金']['amount'];

                payOffData['sums']['業主應付'] = -1 * (payOffData['fees']['清潔費']['amount'] > 0 ? 0 : payOffData['fees']['清潔費']['amount'])
                                        + (payOffData['fees']['滯納金']['amount'] > 0 ? 0 : payOffData['fees']['滯納金']['amount'])
                                        + payOffData['fees']['點交中退盈餘分配']['amount'];   
                                         
                $('[data-subject="沒收押金"]').val(payOffData['fees']['沒收押金']['amount'])
                $('[data-subject="點交中退盈餘分配"]').val(payOffData['fees']['點交中退盈餘分配']['amount'] )
                renderSum()    
            }
            else if( returnWay == '到期退租' ){
                
                payOffData['sums']['業主應付'] = -1 * (payOffData['fees']['清潔費']['amount'] + payOffData['fees']['滯納金']['amount']) +
                payOffData['fees']['管理費']['amount'] + payOffData['sums']['應退金額'];

                payOffData['sums']['應退金額'] = payOffData['fees']['履保金']['amount'] +
                payOffData['fees']['租金']['amount'] +
                payOffData['fees']['清潔費']['amount'] +
                payOffData['fees']['滯納金']['amount'];

                // B49−B56
                payOffData['sums']['兆基應收'] = payOffData['fees']['履保金']['amount'] - payOffData['sums']['應退金額'];
                renderSum()
            }
            else if( returnWay == '協調退租' ){

                payOffData['fees']['點交中退盈餘分配']['amount'] = payOffData['fees']['沒收押金']['amount'] * -1 * (1 - payOffData['withdrawal_revenue_distribution']);
                
                
                payOffData['sums']['應退金額'] = payOffData['fees']['履保金']['amount'] +
                                                payOffData['fees']['沒收押金']['amount'] +
                                                payOffData['fees']['租金']['amount'];
                
                payOffData['sums']['兆基應收'] = payOffData['fees']['履保金']['amount'] - payOffData['sums']['應退金額']['amount'];
                
                payOffData['sums']['業主應付'] = payOffData['sums']['應退金額'] +
                payOffData['fees']['管理費']['amount'] +
                (payOffData['fees']['清潔費']['amount'] + payOffData['fees']['滯納金']['amount']) * -1 +
                payOffData['fees']['點交中退盈餘分配']['amount'];

                $('[data-subject="點交中退盈餘分配"]').val(payOffData['fees']['點交中退盈餘分配']['amount'] )
                renderSum() 
            }
        }
        else if( commissionType == '代管' ){
            if( returnWay == '中途退租' ){
                payOffData['fees']['沒收押金']['amount'] = (
                    payOffData['fees']['履保金']['amount'] +
                    payOffData['fees']['管理費']['amount'] +
                    payOffData['fees']['清潔費']['amount'] +
                    payOffData['fees']['滯納金']['amount']
                ) * -1;
                payOffData['fees']['點交中退盈餘分配']['amount'] = payOffData['fees']['沒收押金']['amount'] * -1 * (1 - payOffData['withdrawal_revenue_distribution']);
                payOffData['sums']['兆基應收'] = payOffData['sums']['業主應付'] = -1 *
                ( payOffData['fees']['清潔費']['amount'] > 0 ? 0 :  payOffData['fees']['清潔費']['amount']) +
                ( payOffData['fees']['滯納金']['amount'] > 0 ? 0 :  payOffData['fees']['滯納金']['amount']) +
                  payOffData['fees']['點交中退盈餘分配']['amount'];    

                $('[data-subject="沒收押金"]').val(payOffData['fees']['沒收押金']['amount'])
                $('[data-subject="點交中退盈餘分配"]').val(payOffData['fees']['點交中退盈餘分配']['amount'] )                  
                renderSum()             
            }
            else if( returnWay == '到期退租' ){
            payOffData['sums']['應退金額'] = payOffData['fees']['履保金']['amount'] +
                payOffData['fees']['租金']['amount'] +
                payOffData['fees']['清潔費']['amount'] +
                payOffData['fees']['滯納金']['amount'];
            // −1×(E54+E52)+E51
            payOffData['sums']['兆基應收'] = -1 * (payOffData['fees']['清潔費']['amount'] + payOffData['fees']['滯納金']['amount']) +
                payOffData['fees']['管理費']['amount'];
            // −1×(B52+B54)+B56+B51
            payOffData['sums']['業主應付'] = -1 * (payOffData['fees']['清潔費']['amount'] + payOffData['fees']['滯納金']['amount']) +
                payOffData['fees']['管理費']['amount'] +
                payOffData['sums']['應退金額'];

                renderSum() 
            }
            else if( returnWay == '協調退租' ){

                // −E32÷2
                payOffData['fees']['沒收押金']['amount'] = -1 * payOffData['fees']['履保金']['amount'] / 2;
                payOffData['fees']['點交中退盈餘分配']['amount'] = payOffData['fees']['沒收押金']['amount'] * -1 * (1 - payOffData['withdrawal_revenue_distribution']);

                // SUM(E32:E34)+SUM(E36:E38)
                payOffData['sums']['應退金額'] = payOffData['fees']['履保金']['amount'] +
                    payOffData['fees']['沒收押金']['amount'] +
                    payOffData['fees']['租金']['amount'] +
                    payOffData['fees']['清潔費']['amount'] +
                    payOffData['fees']['滯納金']['amount'];
                // E39+−1×(E36+E38)+E35
                payOffData['sums']['兆基應收'] = payOffData['fees']['點交中退盈餘分配']['amount'] +
                    -1 * (payOffData['fees']['清潔費']['amount'] + payOffData['fees']['滯納金']['amount']) +
                    payOffData['fees']['管理費']['amount'];
                // E41+E35+(E36+E38)×−1+E39
                payOffData['sums']['業主應付'] = payOffData['sums']['應退金額'] +
                    payOffData['fees']['管理費']['amount'] +
                    (payOffData['fees']['清潔費']['amount'] + payOffData['fees']['滯納金']['amount']) * -1 +
                    payOffData['fees']['點交中退盈餘分配']['amount'];

                $('[data-subject="沒收押金"]').val(payOffData['fees']['沒收押金']['amount'])
                $('[data-subject="點交中退盈餘分配"]').val(payOffData['fees']['點交中退盈餘分配']['amount'] )  
                renderSum() 
            }
        }

    }


</script>
<script>


    const $functionsRow = $('.functions-row');

    (function () {
        const apiURL = '{{ route('payOffs.storePayOffPayments', $tenantContract->id) }}';
        const payOffDate = '{{ optional($payOffDate)->format('Y-m-d')}}';
        const template = `
            <tr class="new-payment">
               <td>
                  <select class="form-control form-control-sm select-subjects" name="subject">
                      @foreach(config('enums.tenant_payments.subject') as $subject)
                        <option value="{{ $subject }}" {{$loop->first ? 'selected' : ''}}>{{ $subject }}</option>
                      @endforeach
                    </select>
                </td>
                <td>
                    <input class="form-control form-control-sm edit-added-item-amount" type="number" name="amount" value="0">
                </td>
                <td>
                    <input class="form-control form-control-sm" type="text" name="comment">
                </td>
                <td>
                    <select class="form-control form-control-sm" name="collected_by">
                    @foreach(config('enums.tenant_payments.collected_by') as $collected_by)
                        <option value="{{ $collected_by }}" {{$loop->first ? 'selected' : ''}}>{{ $collected_by }}</option>
                    @endforeach
                    </select>
                </td>
            </tr>
`;

        $('.js-new-item').on('click', function () {
            $(template).insertBefore($functionsRow);
            setSubjectSelect($('select.select-subjects'));
        });

        $('.js-save-payments').on('click', function () {

            const res_110 = validate_110v.element( "#e_110v" );
            const res_220 = validate_220v.element( "#e_220v" );
            const has_cal_v = $('tr.cal-v').length;
            if (!res_110 || !res_220 || !has_cal_v) {
                alert('請輸入手動抄表的度數，並計算之。');
                return;
            }
            const postData = makeSendData();

            $.post(apiURL, postData, function (data) {
                if (data) {
                    location.reload();
                }
                else {
                    alert('儲存失敗');
                }
            })
        });


    })();

    function makeSendData() {
        return {
            header: {
                pay_off_date: $('#pay-off-date').val(),
                commission_type: $('#commission_type').text(),
                return_ways: $('#return_ways').val(),
                is_monthly_report: $('#is_monthly_report').is(':checked')
            },
            electricity: {
                old_110v: $('span.old-110v').text(),
                old_220v: $('span.old-220v').text(),
                final_110v: $('#e_110v').val(),
                final_220v: $('#e_220v').val(),
            },
            items: createItems(),
            sums: {
                refund_amount: $('#refund_amount').text(),
                should_received: $('#received_amount').text(),
                should_pay: $('#pay_amount').text(),
            }
        };

        function createItems() {
            let items = Array();

            // 從伺服器來的資料
            $('tr.old-payment').each(function (index, item) {
                const temp = {
                    subject: $(item).find('span.subject').text(),
                    collected_by: '',
                    amount: $(item).find('input.edit-new-item-amount').val(),
                    comment: '',
                    is_old: true,
                };

                items.push(temp);
            });

            // 匯費
            const $exchange_fee = $('tr.exchange-fee');
            if ($exchange_fee.length !== 0) {
                const exchange_fee = {
                    subject: '匯費',
                    collected_by: $exchange_fee.find('select[name=collected_by]').val(),
                    amount: $exchange_fee.find('input.exchange-fee-amount').val(),
                    comment: $exchange_fee.find('input.exchange-fee-comment').val(),
                    is_old: false,
                };

                items.push(exchange_fee);
            }

            // 電費
            const $cal_v = $('tr.cal-v');
            const electricity_fee = {
                subject: '電費',
                collected_by: $cal_v.find('select[name=collected_by]').val(),
                amount: $cal_v.find('input.electricity-amount').val(),
                comment: $cal_v.find('input.electricity-comment').val(),
                is_old: false,
            };

            items.push(electricity_fee);

            // 新增項目
            const $new_payment = $('tr.new-payment');
            $new_payment.each(function (index, item) {
                const temp = {
                    subject: $(item).find('select').val(),
                    collected_by: '',
                    amount: $(item).find('input.edit-added-item-amount').val(),
                    comment: $(item).find('input[name=comment]').val(),
                    is_old: false,
                };

                items.push(temp);
            });


            return items;
        }
    }

</script>

<script>
    function setSubjectSelect($select)
    {
        $select.selectize({
            maxItems: 1,
            valueField: 'title',
            labelField: 'title',
            options: [
                    @foreach(config('enums.tenant_payments.subject') as $subject)
                { title: "{{ $subject  }}" },
                @endforeach
            ],
            create: true,
            sortField: 'title',
        });
    }
</script>

<script id="validation">

    $.validator.addMethod('gtEnd110v', function(value, element) {
        return value >= parseInt('{{ $payOffData['110v_end_degree'] }}');
    }, '輸入的110v度數須大於等於上期期末度數');
    $.validator.addMethod('gtEnd220v', function(value, element) {
        return value >= parseInt('{{ $payOffData['220v_end_degree'] }}');
    }, '輸入的220v度數須大於等於上期期末度數');


    const validate_110v = $("#e_110v_form").validate({
        rules: {
            e_110v: {
                required: true,
                gtEnd110v: true,
            }
        },
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
    })

    const validate_220v = $("#e_220v_form").validate({
        rules: {
            e_220v: {
                required: true,
                gtEnd220v: true,
            }
        },
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
    })
</script>
@endsection
