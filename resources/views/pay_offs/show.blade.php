@extends('layouts.app')
@section('content')
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


                    @if(!isset($payOffData))
                        <h3 class="text-center my-5">請選擇上方日期選擇器產生報表</h3>
                    @else
                        <div id="app">
                            <ul class="list-group mb-3">
                                <li class="list-group-item">
                                    <div class="d-inline-flex flex-grow-1 font-weight-bolder">承租方式: </div>
                                    <div id="commission_type" class="d-inline-flex">{{ $headerInfo['commission_type'] }}</div>
                                </li>
                                <li class="list-group-item">
                                    <div class="d-inline-flex font-weight-bolder">退租方式: </div>
                                    <div class="d-inline-flex">
                                        <select v-model="postData.header.return_ways" @change="selectReturnWay">
                                            <option :value="null">
                                                --請選擇--
                                            </option>
                                            <template v-for="return_way in returnWays">
                                                <option :value="return_way">
                                                    @{{ return_way }}
                                                </option>
                                            </template>
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
                            {{--  --}}
                            <table class="table table-bordered">
                                <tbody>
                                <tr>
                                    <th>110v 電費度數</th>
                                    <td colspan="3">
                                        <form id="e_110v_form" onsubmit="return false;">
                                            <div class="input-group w-50">
                                                <span v-if="electricityPaymentMethod != '儲值電表'" class="align-self-center">
                                                    <span class="old-110v">@{{ postData.electricity.old_110v }}</span>
                                                    度
                                                </span>
                                                <input v-model.number="postData.electricity.final_110v"
                                                    :min="postData.electricity.old_110v"
                                                    class="form-control form-control-sm ml-3"
                                                    type="number"
                                                    @change="calculateElectricityPrice"
                                                />
                                                <div class="input-group-append">
                                                    <button
                                                        type="button"
                                                        class="btn btn-sm btn-outline-info"
                                                        @click="calculateElectricityPrice">
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
                                                <span v-if="electricityPaymentMethod != '儲值電表'" class="align-self-center">
                                                    <span class="old-220v">@{{ postData.electricity.old_220v }}</span>
                                                    度
                                                </span>
                                                <input
                                                    v-model.number="postData.electricity.final_220v"
                                                    :min="postData.electricity.old_220v"
                                                    class="form-control form-control-sm ml-3"
                                                    type="number"
                                                    @change="calculateElectricityPrice"
                                                />
                                                <div class="input-group-append">
                                                    <button
                                                        type="button"
                                                        class="btn btn-sm btn-outline-info"
                                                        @click="calculateElectricityPrice">
                                                        計算
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    </td>
                                </tr>
                                <tr class='report-header'>
                                    <th>科目</th>
                                    <th>費用</th>
                                    <th>備註</th>
                                    <th>負擔方</th>
                                </tr>
                                <tr class="old-payment electronic">
                                    <td>
                                        <span class="subject">@{{defaultElectronic.subject}}</span>
                                    </td>
                                    <td>
                                        <input
                                            v-model.number="defaultElectronic.amount"
                                            class="form-control form-control-sm"
                                            type="number"
                                            disabled/>
                                    </td>
                                    <td>
                                        <input
                                            v-model="defaultElectronic.comment"
                                            class="form-control form-control-sm"
                                            type="text"/>
                                    </td>
                                    <td>
                                        <select v-model="defaultElectronic.collected_by" class="form-control form-control-sm">
                                            <template v-for="collected_by in collectedBys">
                                                <option :value="collected_by">@{{ collected_by }}</option>
                                            </template>
                                        </select>
                                    </td>
                                </tr>
                                <tr class="old-payment exchange_fee" v-if="has_exchange_fee" >
                                    <td>
                                        <span class="subject">@{{exchange_fee.subject}}</span>
                                    </td>
                                    <td>
                                        <input
                                            v-model.number="exchange_fee.amount"
                                            class="form-control form-control-sm"
                                            type="number"
                                            disabled/>
                                    </td>
                                    <td>
                                        <input
                                            v-model="exchange_fee.comment"
                                            class="form-control form-control-sm"
                                            type="text"/>
                                    </td>
                                    <td>
                                        <select v-model="exchange_fee.collected_by" class="form-control form-control-sm">
                                            <template v-for="collected_by in collectedBys">
                                                <option :value="collected_by">@{{ collected_by }}</option>
                                            </template>
                                        </select>
                                    </td>
                                </tr>
                                <tr v-for="(item,index) in postData.items" class="old-payment">
                                    <template v-if="item.is_showed === true && item.is_tenant === true">
                                        <td>
                                            <button class="btn btn-rounded btn-icon"
                                                    v-if="item.is_deleted"
                                                    @click="delItem(index)">
                                                <i class="typcn typcn-delete-outline text-danger"></i>
                                            </button>
                                            <span class="subject">@{{ item.subject }}</span>
                                        </td>
                                        <td>
                                            <input
                                                v-model.number="item.amount"
                                                class="form-control form-control-sm"
                                                type="number"/>
                                        </td>
                                        <td>
                                            <input
                                                v-model="item.comment"
                                                class="form-control form-control-sm"
                                                type="text"/>
                                        </td>
                                        <td>
                                            <select v-model="item.collected_by" class="form-control form-control-sm">
                                                <template v-for="collected_by in collectedBys">
                                                    <option :value="collected_by">@{{ collected_by }}</option>
                                                </template>
                                            </select>
                                        </td>
                                    </template>
                                </tr>

                                <tr class="functions-row">
                                    <td>
                                        <select v-model="subject" class="form-control form-control-sm">
                                            <option :value="null">--請選擇--</option>

                                            <template v-for="subject in subjects">
                                                <option :value="subject">@{{ subject }}</option>
                                            </template>
                                        </select>
                                    </td>
                                    <td colspan="4" class="text-center">
                                        <button class="btn btn-success btn-xs"
                                                :disabled="subject==null"
                                                @click="addItem">
                                                新增項目
                                        </button>
                                        <label for="exchange_fee">匯費</label>
                                        <input v-model="has_exchange_fee"  type="checkbox">
                                    </td>
                                </tr>
                                <tr>
                                    <th>應退房客金額</th>
                                    <td colspan="3">
                                        <div class="d-inline-flex">
                                            <div class="align-content-center">
                                                <input
                                                    v-model.number="refund_amount"
                                                    type="number"
                                                    value="{{ $payOffData['sums']['應退金額'] }}"
                                                    disabled/>
                                                <span>元</span>
                                                <button id="edit_refund" class="btn btn-xs btn-primary">編輯</button>
                                                <button id="update_refund" class="btn btn-xs btn-info d-none">確認</button>
                                                <button class="btn btn-xs btn-secondary" @click="init">
                                                    恢復
                                                </button>
                                            </div>
                                        </div>
                                    </td>
                                </tr>

                                <tr v-for="item in postData.items" class="old-payment">
                                    <template v-if="item.is_showed === true && item.is_tenant === false">
                                        <td>
                                            <span class="subject">@{{ item.subject }}</span>
                                        </td>
                                        <td>
                                            <input
                                                v-model.number="item.amount"
                                                class="form-control form-control-sm"
                                                type="number"/>
                                        </td>
                                        <td>
                                            <input
                                                v-model="item.comment"
                                                class="form-control form-control-sm"
                                                type="text"/>
                                        </td>
                                        <td></td>
                                    </template>
                                </tr>
                                <tr>
                                    <th>兆基應收</th>
                                    <td colspan="3">
                                        <div class="d-inline-flex">
                                            <div class="align-content-center">
                                                <input
                                                    v-model.number="postData.sums.should_received"
                                                    type="number"
                                                    disabled/>
                                                <span>元</span>
                                                <button id="edit_received" class="btn btn-xs btn-primary">編輯</button>
                                                <button id="update_received" class="btn btn-xs btn-info d-none">確認</button>
                                                <button id="reset_received" class="btn btn-xs btn-secondary">恢復</button>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th>業主應付</th>
                                    <td colspan="3">
                                        <div class="d-inline-flex">
                                            <div class="align-content-center">
                                                <input
                                                    v-model.number="postData.sums.should_pay"
                                                    type="number"
                                                    disabled/>
                                                <span>元</span>
                                                <button id="edit_pay" class="btn btn-xs btn-primary">編輯</button>
                                                <button id="update_pay" class="btn btn-xs btn-info d-none">確認</button>
                                                <button id="reset_pay" class="btn btn-xs btn-secondary">恢復</button>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                            {{--  --}}
                            <div class="text-center">
                                <button class="d-inline-block my-3 mx-auto btn btn-lg btn-info" @click="savePayments">儲存</button>
                                <input v-model="postData.header.is_doubtful" type="checkbox"/> 是否為呆帳
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('js')
<link rel="stylesheet" href="{{asset('vendors/jquery-toast-plugin/jquery.toast.min.css')}}">
<script src="{{asset('vendors/jquery-toast-plugin/jquery.toast.min.js')}}"></script>
<script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>


<script>
    const apiURL = '{{ route('payOffs.storePayOffPayments', $tenantContract->id) }}';
    const tenantContractsId = '{{ $tenantContract->id }}';
    const payOffData = @json($payOffData,JSON_UNESCAPED_UNICODE);
    const collected_bys = @json(config('enums.tenant_payments.collected_by'),JSON_UNESCAPED_UNICODE);
    const subjects = @json(config('enums.tenant_payments.subject'),JSON_UNESCAPED_UNICODE);
    const return_ways = @json(config('enums.pay_logs.return_ways'),JSON_UNESCAPED_UNICODE);
    const payOffDate = "{{optional($payOffDate)->format('Y-m-d')}}";
    const commission_type = "{{$headerInfo['commission_type']}}"
    const electricity_payment_method = "{{$tenantContract->room->building->electricity_payment_method}}"

    if (payOffDate) {
        var app = new Vue({
            el: '#app',
            data(){
                return {
                    apiURL: apiURL,
                    tenantContractsId: tenantContractsId,
                    electricityPaymentMethod: electricity_payment_method,
                    returnWays:return_ways,
                    collectedBys: collected_bys,
                    subjects:subjects,
                    subject: null,
                    payOffData :payOffData,
                    postData:{
                        electricity:{
                            final_110v:0,
                            final_220v:0,
                            old_110v: payOffData['110v_end_degree'],
                            old_220v: payOffData['220v_end_degree'],
                        },
                        header:{
                            pay_off_date: payOffDate,
                            commission_type: commission_type,
                            return_ways: null,
                            is_doubtful: false,
                        },
                        items: [],
                        sums:{
                            refund_amount: payOffData.sums['應退金額'],
                            should_received: payOffData.sums['兆基應收'],
                            should_pay: payOffData.sums['業主應付'],
                        }
                    },
                    has_exchange_fee: false,
                    exchange_fee:{
                        subject:'匯費',
                        amount: -30,
                        collected_by: '公司',
                        comment: '',
                    },
                    defaultElectronic:{
                        subject:'電費',
                        amount: 0,
                        collected_by: '房東',
                        comment: '',
                    },
                    item: {
                        subject: '',
                        amount: 0,
                        collected_by: '',
                        comment: '',
                        is_deleted: true
                    },
                    electricityDegree:{
                        method: '固定',
                        pricePerDegree: 0,
                        pricePerDegreeSummer: 0
                    }
                }
            },
            computed: {
                refund_amount:{
                    get(){
                        return this.postData.sums.refund_amount
                    },
                    set(){
                        this.postData.sums.refund_amount
                    }
                }
            },
            watch: {
                postData:{
                    deep:true,
                    handler:function(){
                        this.reCountSum()
                    }
                }
            },
            mounted() {
                if (this.postData.header.pay_off_date) {
                    this.init()
                }
            },
            methods: {
                addItem(){
                    var subject = Object.assign({},this.item,{subject:this.subject,is_showed:true,is_tenant:true})
                    this.postData.items.push(subject)
                },
                delItem(index){
                    this.postData.items = this.postData.items.filter((item,idx) => index !== idx)
                },
                init(){
                    var payOffDate = window.myQueryString().getQueryStrings()['payOffDate'];
                    var returnWays = window.myQueryString().getQueryStrings()['return_ways'];
                    this.postData.header.pay_off_date = payOffDate ? payOffDate : null
                    this.postData.header.return_ways = returnWays ? returnWays : null

                    // 電費初始化
                    this.postData.electricity.old_110v = payOffData['110v_end_degree']
                    this.postData.electricity.old_220v = payOffData['220v_end_degree']
                    if (this.electricityPaymentMethod !== '儲值電表') {
                        this.postData.electricity.final_110v = payOffData['110v_end_degree']
                        this.postData.electricity.final_220v = payOffData['220v_end_degree']
                    }
                    this.calculateElectricityPrice(this.electricityPaymentMethod)

                    this.postData.items = JSON.parse(JSON.stringify(Object.values(payOffData.fees)))
                    this.postData.sums.refund_amount = payOffData.sums['應退金額']
                    this.postData.sums.should_received = payOffData.sums['兆基應收']
                    this.postData.sums.should_pay = payOffData.sums['業主應付']

                    // this.postData.items.unshift(this.defaultElectronic)
                },
                selectReturnWay(event){
                    selectedText = event.target.value
                    var url = new URL(location.href);
                    var pay_off_date = this.postData.header.pay_off_date
                    var path =  url.protocol + '//' + url.host + url.pathname + `?payOffDate=`+pay_off_date+`&return_ways=${selectedText}`;
                    location.href = path
                },
                countTenantPayment(){
                    var returnWay = this.postData.header.return_ways
                    var originalRefund = this.postData.sums.refund_amount;
                    var addedAmount = this.postData.items;
                    let sum = parseInt(originalRefund);
                    if( returnWay != "中途退租" ){
                        addedAmount.forEach(function (element, key) {
                            sum = _.add(sum, parseInt(element.amount) );
                        });
                    }
                    // console.log(returnWay,originalRefund, sum);
                    return sum
                },
                calculateElectricityPrice(mode = '普通'){
                    var input_110v = parseInt(this.postData.electricity.final_110v)
                    var input_220v = parseInt(this.postData.electricity.final_220v)
                    if ( Number.isNaN(input_110v)) {
                        this.postData.electricity.final_110v = input_110v = 0
                    }
                    if ( Number.isNaN(input_220v)) {
                        this.postData.electricity.final_220v = input_220v = 0
                    }

                    $.get('/tenantContracts/' + this.tenantContractsId + '/electricityDegree').then(data =>{
                        this.electricityDegree = Object.assign(this.electricityDegree, data);

                        var pricePerDegree = data.pricePerDegree || 0;
                        var pricePerDegreeSummer = data.pricePerDegreeSummer || 0;
                        var readMonth = (new Date).getMonth() + 1;
                        var ratio = [7, 8, 9, 10].includes(readMonth) ? pricePerDegreeSummer : pricePerDegree;
                        var e_110v_end = parseInt(this.postData.electricity.old_110v)
                        var e_220v_end = parseInt(this.postData.electricity.old_220v)

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

                        this.defaultElectronic.amount = amount * -1
                        // console.log(mode,pricePerDegree,pricePerDegreeSummer,readMonth,ratio,amount);
                    })
                },
                savePayments(){
                    $.post(apiURL, this.postData).then((response,textStatus,jqXHR) => {
                        // console.log(response,textStatus,jqXHR);
                        $.toast({
                            heading: 'Success',
                            text: '儲存成功',
                            showHideTransition: 'slide',
                            icon: 'success',
                            loaderBg: '#f96868',
                            position: 'top-right'
                        })
                    }).catch((error) => {
                        // console.log(error.message);
                        var responseJSON = error.responseJSON
                        for (const key in responseJSON.errors) {
                            if (responseJSON.errors.hasOwnProperty(key)) {
                                const element = responseJSON.errors[key];
                                $.toast({
                                    heading: 'Warning',
                                    text: element[0],
                                    showHideTransition: 'slide',
                                    icon: 'warning',
                                    loaderBg: '#57c7d4',
                                    position: 'top-right'
                                })
                            }
                        }
                    })
                },
                reCountSum(){
                    var commissionType = this.postData.header.commission_type
                    var returnWay = this.postData.header.return_ways
                    var withdrawal_revenue_distribution = this.payOffData.withdrawal_revenue_distribution
                    var initItems = [
                        '履保金', '管理費', '折抵管理費', '清潔費', '折抵清潔費', '滯納金', '折抵滯納金', '沒收押金', '點交中退盈餘分配', '租金', '電費'
                    ]
                    var extraItems = Object.keys(this.payOffData.fees).filter(item => !initItems.includes(item));
                    var extraAmount = 0
                    if (extraItems.length > 0) {
                        extraAmount = extraItems.map((value, index) =>{
                            return this.payOffData.fees[value]['amount']
                        }).reduce((a,b) => a + b)
                    }

                    console.log(commissionType,returnWay,extraAmount);
                    if( commissionType == '包租' ){
                        if( returnWay == '中途退租' ){
                            this.postData.items.forEach((item, index, items) => {
                                // -(履保金+管理費+清潔費+設備+滯納金)
                                if(item.subject == '沒收押金'){
                                    item.amount = (items.reduce((accumulator, current) => {
                                                        if(['履保金','清潔費','滯納金','租金'].indexOf(current.subject) > -1){
                                                            return Math.floor(current.amount) + accumulator
                                                        }
                                                        return accumulator
                                                    },0) + extraAmount) * -1
                                }
                                // ( 沒收押金 * -1 * ( 1 - landlordContract - withdrawal_revenue_distribution ) )
                                if(item.subject == '點交中退盈餘分配'){
                                    item.amount = items.reduce((accumulator, current) => {
                                                        if(['沒收押金'].indexOf(current.subject) > -1){
                                                            return Math.floor(current.amount * -1 * (1 - withdrawal_revenue_distribution)) + accumulator
                                                        }
                                                        return accumulator
                                                    },0)
                                }
                            });
                            this.postData.sums.should_received = this.postData.items.reduce((accumulator, current) => {
                                                                if(['清潔費','滯納金','點交中退盈餘分配'].indexOf(current.subject) > -1){
                                                                    return Math.floor(current.amount) + accumulator
                                                                }
                                                                return accumulator
                                                            },0)

                            this.postData.sums.should_pay = this.postData.sums.should_received + this.postData.sums.refund_amount
                        }
                        else if( returnWay == '到期退租' ){
                            this.postData.sums.refund_amount = this.postData.items.reduce((accumulator, current) => {
                                                                            if(['履保金','租金','清潔費','滯納金'].indexOf(current.subject) > -1){
                                                                                return Math.floor(current.amount) + accumulator
                                                                            }
                                                                            return accumulator
                                                                        },0) + extraAmount
                            // B49−B56
                            this.postData.sums.should_received = this.postData.items.reduce((accumulator, current) => {
                                                                        if(['管理費'].indexOf(current.subject) > -1){
                                                                            return Math.floor(current.amount) + accumulator
                                                                        }
                                                                        if (['清潔費','滯納金'].indexOf(current.subject) > -1) {
                                                                            return Math.floor(-1*current.amount) + accumulator
                                                                        }
                                                                        return accumulator
                                                                    },0)
                            //
                            this.postData.sums.should_pay = this.postData.items.reduce((accumulator, current) => {
                                                                if (['清潔費','滯納金'].indexOf(current.subject) > -1) {
                                                                    return Math.floor(-1*current.amount) + accumulator
                                                                }
                                                                return accumulator
                                                            },0) + this.postData.sums.refund_amount
                        }
                        else if( returnWay == '協調退租' ){
                            this.postData.items.forEach((item, index, items) => {
                                if(item.subject == '沒收押金'){
                                    item.amount = items.reduce((accumulator, current) => {
                                                        if(['履保金'].indexOf(current.subject) > -1){
                                                            return Math.floor(-1*(current.amount) /2) + accumulator
                                                        }
                                                        return accumulator
                                                    },0)
                                }
                                if(item.subject == '點交中退盈餘分配'){
                                    item.amount = items.reduce((accumulator, current) => {
                                                        if(['沒收押金'].indexOf(current.subject) > -1){
                                                            return Math.floor(current.amount * -1 * (1 - withdrawal_revenue_distribution)) + accumulator
                                                        }
                                                        return accumulator
                                                    },0)
                                }
                            });
                            this.postData.sums.refund_amount = this.postData.items.reduce((accumulator, current) => {
                                                                    if(['履保金','沒收押金','租金','清潔費','滯納金'].indexOf(current.subject) > -1){
                                                                        return Math.floor(current.amount) + accumulator
                                                                    }
                                                                    return accumulator
                                                                },0) + extraAmount
                            this.postData.sums.should_received = this.postData.items.reduce((accumulator, current) => {
                                                                    if(['清潔費','點交中退盈餘分配'].indexOf(current.subject) > -1){
                                                                        return Math.floor(current.amount) + accumulator
                                                                    }
                                                                    if (['滯納金'].indexOf(current.subject) > -1) {
                                                                        return Math.floor(-1*current.amount) + accumulator
                                                                    }
                                                                    return accumulator
                                                                },0)
                            this.postData.sums.should_pay = this.postData.items.reduce((accumulator, current) => {
                                                                if(['清潔費','點交中退盈餘分配'].indexOf(current.subject) > -1){
                                                                    return Math.floor(current.amount) + accumulator
                                                                }
                                                                if (['滯納金'].indexOf(current.subject) > -1) {
                                                                    return Math.floor(-1*current.amount) + accumulator
                                                                }
                                                                return accumulator
                                                            },0) + this.postData.sums.refund_amount
                        }
                    }
                    else if( commissionType == '代管' ){
                        if( returnWay == '中途退租' ){
                            this.postData.items.forEach((item, index, items) => {
                                if(item.subject == '沒收押金'){
                                    item.amount = (items.reduce((accumulator, current) => {
                                                        if(['履保金','租金','清潔費','滯納金'].indexOf(current.subject) > -1){
                                                            return Math.floor(current.amount) + accumulator
                                                        }
                                                        return accumulator
                                                    },0) + extraAmount) * -1
                                }
                                if(item.subject == '點交中退盈餘分配'){
                                    item.amount = items.reduce((accumulator, current) => {
                                                        if(['沒收押金'].indexOf(current.subject) > -1){
                                                            return Math.floor(current.amount * -1 * (1 - withdrawal_revenue_distribution)) + accumulator
                                                        }
                                                        return accumulator
                                                    },0)
                                }
                            });
                            this.postData.sums.should_received = this.postData.items.reduce((accumulator, current) => {
                                                                if(['管理費','清潔費','點交中退盈餘分配'].indexOf(current.subject) > -1){
                                                                    return Math.floor(current.amount) + accumulator
                                                                }
                                                                if (['滯納金'].indexOf(current.subject) > -1) {
                                                                    return Math.floor(-1*current.amount) + accumulator
                                                                }
                                                                return accumulator
                                                            },0)

                            this.postData.sums.should_pay = this.postData.sums.should_received + this.postData.sums.refund_amount
                        }
                        else if( returnWay == '到期退租' ){
                            this.postData.sums.refund_amount = this.postData.items.reduce((accumulator, current) => {
                                                                            if(['履保金','租金','清潔費','滯納金'].indexOf(current.subject) > -1){
                                                                                return Math.floor(current.amount) + accumulator
                                                                            }
                                                                            return accumulator
                                                                        },0) + extraAmount
                            // −1×(E54+E52)+E51
                            this.postData.sums.should_received = this.postData.items.reduce((accumulator, current) => {
                                                                        if(['管理費'].indexOf(current.subject) > -1){
                                                                            return Math.floor(current.amount) + accumulator
                                                                        }
                                                                        if (['清潔費','滯納金'].indexOf(current.subject) > -1) {
                                                                            return Math.floor(-1*current.amount) + accumulator
                                                                        }
                                                                        return accumulator
                                                                    },0)

                            // −1×(B52+B54)+B56+B51
                            this.postData.sums.should_pay = this.postData.items.reduce((accumulator, current) => {
                                                                if(['管理費'].indexOf(current.subject) > -1){
                                                                    return Math.floor(current.amount) + accumulator
                                                                }
                                                                if (['清潔費','滯納金'].indexOf(current.subject) > -1) {
                                                                    return Math.floor(-1*current.amount) + accumulator
                                                                }
                                                                return accumulator
                                                            },0) + this.postData.sums.refund_amount
                        }
                        else if( returnWay == '協調退租' ){
                            // −E32÷2
                            this.postData.items.forEach((item, index, items) => {
                                if(item.subject == '沒收押金'){
                                    item.amount = items.reduce((accumulator, current) => {
                                                        if(['履保金'].indexOf(current.subject) > -1){
                                                            return Math.floor(-1*(current.amount) /2) + + accumulator
                                                        }
                                                        return accumulator
                                                    },0)
                                }
                                if(item.subject == '點交中退盈餘分配'){
                                    item.amount = items.reduce((accumulator, current) => {
                                                        if(['沒收押金'].indexOf(current.subject) > -1){
                                                            return Math.floor(current.amount * -1 * (1 - withdrawal_revenue_distribution)) + accumulator
                                                        }
                                                        return accumulator
                                                    },0)
                                }
                            });

                            // SUM(E32:E34)+SUM(E36:E38)
                            this.postData.sums.refund_amount = this.postData.items.reduce((accumulator, current) => {
                                                                    if(['履保金','沒收押金','租金','清潔費','滯納金'].indexOf(current.subject) > -1){
                                                                        return Math.floor(current.amount) + accumulator
                                                                    }
                                                                    return accumulator
                                                                },0) + extraAmount
                            // E39+−1×(E36+E38)+E35
                            this.postData.sums.should_received = this.postData.items.reduce((accumulator, current) => {
                                                                    if(['點交中退盈餘分配','管理費'].indexOf(current.subject) > -1){
                                                                        return Math.floor(current.amount) + accumulator
                                                                    }
                                                                    if (['清潔費','滯納金'].indexOf(current.subject) > -1) {
                                                                        return Math.floor(-1*current.amount) + accumulator
                                                                    }
                                                                    return accumulator
                                                                },0)

                            // E41+E35+(E36+E38)×−1+E39
                            this.postData.sums.should_pay = this.postData.items.reduce((accumulator, current) => {
                                                                if(['管理費','點交中退盈餘分配'].indexOf(current.subject) > -1){
                                                                    return Math.floor(current.amount) + accumulator
                                                                }
                                                                if (['清潔費','滯納金'].indexOf(current.subject) > -1) {
                                                                    return Math.floor(-1*current.amount) + accumulator
                                                                }
                                                                return accumulator
                                                            },0) + this.postData.sums.refund_amount
                        }
                    }
                },
            },
        })
    }
</script>
@endpush

@endsection
