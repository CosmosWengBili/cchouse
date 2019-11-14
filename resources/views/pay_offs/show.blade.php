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
                        <div id="app">
                            <table class="table table-bordered">
                                <tbody>
                                <tr>
                                    <th>110v 電費度數</th>
                                    <td colspan="3">
                                        <form id="e_110v_form" onsubmit="return false;">
                                            <div class="input-group w-50">
                                                <span class="align-self-center">
                                                    <span class="old-110v">@{{ postData.electricity.old_110v }}</span>
                                                    度
                                                </span>
                                                <input v-model="postData.electricity.final_110v"
                                                    :min="postData.electricity.old_110v"
                                                    class="form-control form-control-sm ml-3"
                                                    type="number"
                                                />
                                                <div class="input-group-append">
                                                    <button
                                                        type="button"
                                                        class="btn btn-sm btn-outline-info"
                                                        @click="calculateElectricityPrice"
                                                    >
                                                        </span>
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
                                                <span class="align-self-center">
                                                    <span class="old-220v">@{{ postData.electricity.old_220v }}</span>
                                                    度
                                                </span>
                                                <input
                                                    v-model="postData.electricity.final_220v"
                                                    :min="postData.electricity.old_220v"
                                                    class="form-control form-control-sm ml-3"
                                                    type="number"
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
                                <tr v-for="(item,index) in postData.items" class="old-payment">
                                    <template v-if="item.is_showed === true && item.is_tenant === true">
                                        <td>
                                            <button class="btn btn-rounded btn-icon"
                                                    @click="delItem(index)">
                                                <i class="typcn typcn-delete-outline text-danger"></i>
                                            </button>
                                            <span class="subject">@{{ item.subject }}</span>
                                        </td>
                                        <td>
                                            <input
                                                v-model="item.amount"
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
                                        <input type="checkbox" id="exchange_fee" >
                                    </td>
                                </tr>
                                <tr>
                                    <th>應退房客金額</th>
                                    <td colspan="3">
                                        <div class="d-inline-flex">
                                            <div class="align-content-center">
                                                <input
                                                    v-model="postData.sums.refund_amount"
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
                                                v-model="item.amount"
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
                                                    v-model="postData.sums.should_received"
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
                                                    v-model="postData.sums.should_pay"
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
    const return_way = "{{$return_way}}";
    const payOffDate = "{{optional($payOffDate)->format('Y-m-d')}}";
    const commission_type = "{{$headerInfo['commission_type']}}"

    var app = new Vue({
        el: '#app',
        data(){
            return {
                apiURL: apiURL,
                tenantContractsId: tenantContractsId,
                returnWay: return_way,
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
                        return_ways: return_way,
                        is_doubtful: false,
                    },
                    items: [],
                    sums:{
                        refund_amount: payOffData.sums['應退金額'],
                        should_received: payOffData.sums['兆基應收'],
                        should_pay: payOffData.sums['業主應付'],
                    }
                },
                item: {
                    subject:'',
                    amount:0,
                    collected_by:'',
                    comment:'',
                },
                electricityDegree:{
                    method: '固定',
                    pricePerDegree: 0,
                    pricePerDegreeSummer: 0
                }
            }
        },
        mounted() {
            this.init()
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
                this.calculateElectricityPrice()
                this.postData.items = JSON.parse(JSON.stringify(Object.values(payOffData.fees)))
                this.postData.electricity.old_110v = payOffData['110v_end_degree']
                this.postData.electricity.old_220v = payOffData['220v_end_degree']
                this.postData.sums.refund_amount = payOffData.sums['應退金額']
                this.postData.sums.should_received = payOffData.sums['兆基應收']
                this.postData.sums.should_pay = payOffData.sums['業主應付']
            },
            calculateElectricityPrice(mode = '普通'){
                $.get('/tenantContracts/' + this.tenantContractsId + '/electricityDegree').then(response =>{
                    this.electricityDegree = Object.assign(this.electricityDegree,response);
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
            }
        },
    })
</script>
@endpush

@endsection
