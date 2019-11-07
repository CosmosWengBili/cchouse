<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
</head>
<body>
    <style>
    .monthly-report div{
        margin-top: 0.2rem;
        margin-bottom: 0.25rem;
    }
    .monthly-report .room > div{
        min-height: 100px;
    }
    .monthly-report #detail-data span.left-bottom{
        position: absolute;
        top: 40px;
        right: 5%;
        /* top: 80px;
        left: 85%; */
    }

    .monthly-report .room span.left-bottom{
        position: absolute;
        top: 80px;
        right: 5%;
        /* top: 80px;
        left: 85%; */
    }

    /* used for only pdf format */
    body{
        font-size: 12px;
    }
    .monthly-report .flex{
        display: -webkit-box;
    }
    .row{
        display: flex;
        flex-wrap: wrap;
        padding-left: 15px;
        padding-right: 15px;
    }
    .bg-gray{
        background-color: #EEEEEE !important;
    }
    .bg-highlight{
        background-color: yellow !important;
    }
    .border{
        border: 1px solid #000000 !important;
    }
    .border-top {
        border-top: 1px solid #000000 !important;
    }
    .border-bottom {
        border-bottom: 1px solid #000000 !important;
    }
    .px-0{
        padding-right: 0 !important;
        padding-left: 0 !important;
    }
    .py-4{
        padding-bottom: 2.5rem !important;
        padding-top: 2.5rem !important
    }
    .py-5{
        padding-bottom: 3rem !important;
        padding-top: 3rem !important;
    }
    .my-0{
        margin-bottom: 0 !important;
        margin-top: 0 !important;
    }
    .relative{
        position: relative;
    }
    .bottom{
        position: absolute;
        bottom: 0%;
    }
    .font-weight-bold{
        font-weight: bold;
    }
    .electricity-report div{
        border: 1px solid gray;
    }

    </style>
    <div class="container-fluid" style="min-width: 1100px;">
        <div class="card">
            <div class="card-body table-responsive" style="padding: 2rem;">
                <div class="row justify-content-center monthly-report mt-3">
                    {{-- Header --}}
                    <div class="col-xs-3">
                        <img src="/images/monthly_report_logo.png" style="width: 100%;">
                    </div>
                    <div class="col-xs-6 text-center">
                        <h2>綜合月結單</h2>
                    </div>
                    <div class="col-xs-3"></div>
                    <div class="col-xs-12 text-right border-bottom border-dark">
                        <p class="mr-4">
                            {{$data['report_used_date']['year'] - 1911}}年 <span class="bg-highlight px-3 text-center">{{$data['report_used_date']['month']}}</span>月份
                        </p>
                    </div>
                    {{-- Header end --}}
                    {{-- Meta data --}}
                    <div class="col-xs-1">客戶姓名</div>
                    <div class="col-xs-3">{{implode(",", $data['meta']['landlord_name']->toArray())}}</div>
                    <div class="col-xs-1">管理期間</div>
                    <div class="col-xs-4">{{$data['meta']['period']}}</div>
                    <div class="col-xs-3">&nbsp;</div>

                    <div class="col-xs-1">物件代碼</div>
                    <div class="col-xs-8">{{$data['meta']['building_code']}}</div>
                    <div class="col-xs-1 text-center bg-gray">本月收入</div>
                    <div class="col-xs-2">{{ number_format($data['meta']['total_income']) }}</div>

                    <div class="col-xs-1">物件地址</div>
                    <div class="col-xs-11">{{$data['meta']['building_location']}}</div>

                    <div class="col-xs-1">管理戶數</div>
                    <div class="col-xs-3">{{$data['meta']['rooms_count']}}</div>
                    <div class="col-xs-1">聯絡電話</div>
                    <div class="col-xs-4">{{implode(",", $data['meta']['landlords_phones']->toArray())}}</div>
                    <div class="col-xs-1 text-center bg-gray">本月支出</div>
                    <div class="col-xs-2">{{ number_format($data['meta']['total_expense']) }}</div>

                    <div class="col-xs-12">&nbsp;</div>

                    <div class="col-xs-1">匯款帳號</div>
                    <div class="col-xs-8">{{implode(",", $data['meta']['account_numbers'])}}</div>
                    <div class="col-xs-1 text-center bg-gray">
                        @if( $data['meta']['total_income'] - $data['meta']['total_expense'] > 0 )
                            本月實收
                        @else
                            本月應付
                        @endif
                    </div>
                    <div class="col-xs-2">{{ number_format( $data['meta']['total_income'] - $data['meta']['total_expense']) }}</div>

                    <div class="col-xs-12">&nbsp;</div>

                    <div class="col-xs-2">帳單郵寄或傳真</div>
                    <div class="col-xs-7">{{implode(",", $data['meta']['account_address'])}}</div>
                    <div class="col-xs-1 px-1 text-center bg-gray">約定入帳日</div>
                    <div class="col-xs-2 bg-highlight px-3">{{$data['report_used_date']['next_month']}}月{{$data['meta']['rent_collection_time']}}日</div>
                    {{-- Meta data end --}}
                    {{-- Room data --}}
                    <div class="col-xs-12 px-0 text-center bg-gray mb-0">
                        <div class="col-xs-2"></div>
                        <div class="col-xs-10 px-0">
                            <div class="col-xs-1"></div>
                            <div class="col-xs-5 text-left">說明</div>
                            <div class="col-xs-2">入帳日</div>
                            <div class="col-xs-2">收入</div>
                            <div class="col-xs-2">支出</div>
                        </div>
                    </div>
                    @foreach( $data['rooms'] as $room )
                    <div class="col-xs-12 px-0 room border border-dark">
                        <div class="col-xs-2 text-center border border-dark py-5 my-0">
                            {{$room['meta']['room_number']}}室<br/>
                            @if( $room['meta']['management_fee_mode'] == "比例" )
                                (服務費率 {{ $room['meta']['management_fee'] }} %)
                            @elseif( $room['meta']['management_fee_mode'] == "固定" )
                                (服務費 {{ number_format($room['meta']['management_fee']) }})
                            @endif
                            <br/>
                            狀態:  {{ $room['meta']['status'] }}
                        </div>
                        <div class="col-xs-10 px-0">
                            @php
                                $income_not_zero = count($room['incomes']) > 0;
                                $expense_not_zero = count($room['expenses']) > 0;
                            @endphp
                            @foreach( $room['incomes'] as $income )
                                <div class="col-xs-6 px-5">{{ $income['subject'] }}( {{ $income['month'] }} )</div>
                                <div class="col-xs-2 text-center">{{ $income['paid_at']->format('m-d') }}</div>
                                <div class="col-xs-2 text-right">{{ number_format($income['amount']) }}</div>
                                <div class="col-xs-2 text-right"></div>
                            @endforeach
                            @if( $income_not_zero )
                                <div class="col-xs-12 border border-dark ml-3 mb-0" style="height: 0px;"></div>
                            @endif
                            @foreach( $room['expenses'] as $expense )
                                <div class="col-xs-6 px-5">{{ $expense['subject'] }}</div>
                                <div class="col-xs-2 text-center">{{ $expense['paid_at']->format('m-d') }}</div>
                                <div class="col-xs-2 text-right"></div>
                                <div class="col-xs-2 text-right">{{ number_format($expense['amount']) }}</div>
                            @endforeach
                            @if( $expense_not_zero )
                                <div class="col-xs-12 border border-dark ml-3 mb-0" style="height: 0px;"></div>
                                <div class="col-xs-6 bottom"></div>
                                <div class="col-xs-2 text-center bottom" style="left: 50%;"><span>小計</span></div>
                                <div class="col-xs-2 text-right bottom" style="left: 67%;"><span>{{ number_format($room['meta']['room_total_income']) }}</span></div>
                                <div class="col-xs-2 text-right bottom" style="left: 84%;"><span>{{ number_format($room['meta']['room_total_expense']) }}</span></div>
                            @else
                                <div class="col-xs-6"></div>
                                <div class="col-xs-2 text-center"><span class="left-bottom">小計</span></div>
                                <div class="col-xs-2 text-right"><span class="left-bottom">{{ number_format($room['meta']['room_total_income']) }}</span></div>
                                <div class="col-xs-2 text-right"><span class="left-bottom">{{ number_format($room['meta']['room_total_expense']) }}</span></div>
                            @endif
                    </div>
                    </div>
                    @endforeach
                    {{-- Room data end --}}
                    {{-- PayOff data --}}
                    @foreach( $data['payoffs'] as $payoff )
                    @php
                        $income_not_zero = count($payoff['incomes']) > 0;
                        $expense_not_zero = count($payoff['expenses']) > 0;
                    @endphp
                    <div class="col-xs-12 px-0 room border border-dark">
                        <div class="col-xs-2 text-center border border-dark py-5 my-0">
                            {{$payoff['meta']['room_number']}}室(點交)<br/>
                        </div>
                        <div class="col-xs-10 px-0">
                            @foreach( $payoff['incomes'] as $income )
                                <div class="col-xs-6 px-5">{{ $income['subject'] }}( {{ $income['month'] }} )</div>
                                <div class="col-xs-2 text-center">{{ $income['paid_at']->format('m-d') }}</div>
                                <div class="col-xs-2 text-right">{{ number_format($income['amount']) }}</div>
                                <div class="col-xs-2 text-right"></div>
                            @endforeach
                            @if( count($payoff['incomes']) > 0 )
                                <div class="col-xs-12 border border-dark ml-3 mb-0" style="height: 0px;"></div>
                            @endif

                            @foreach( $payoff['expenses'] as $expense )
                                <div class="col-xs-6 px-5">{{ $expense['subject'] }}</div>
                                <div class="col-xs-2 text-center">{{ $expense['paid_at']->format('m-d') }}</div>
                                <div class="col-xs-2 text-right"></div>
                                <div class="col-xs-2 text-right">{{ number_format($expense['amount']) }}</div>
                            @endforeach
                            @if( count($payoff['expenses']) > 0 )
                                <div class="col-xs-12 border border-dark ml-3 mb-0 bottom" style="height: 0px;"></div>
                            @endif
                            <div class="col-xs-6 bottom"></div>
                            <div class="col-xs-2 text-center bottom" style="left: 50%;"><span class="{{ ($expense_not_zero || $income_not_zero) == false ? 'left-bottom' : ''  }}">小計</span></div>
                            <div class="col-xs-2 text-right bottom" style="left: 67%;"><span class="{{ ($expense_not_zero || $income_not_zero) == false ? 'left-bottom' : ''  }}">{{ number_format($payoff['meta']['room_total_income']) }}</span></div>
                            <div class="col-xs-2 text-right bottom" style="left: 84%;"><span class="{{ ($expense_not_zero || $income_not_zero) == false ? 'left-bottom' : ''  }}">{{ number_format($payoff['meta']['room_total_expense']) }}</span></div>
                    </div>
                    </div>
                    @endforeach
                    {{-- PayOff data end --}}
                    {{-- Detail data --}}
                    <div class="col-xs-12 px-0 border border-dark">
                        <div class="col-xs-2 text-center border border-dark py-5 my-0">
                            費用明細
                        </div>
                        <div id="detail-data" class="col-xs-10 align-self-start px-0">
                            <input type="hidden"
                                id="total_landlord_other_subject_id"
                                value={{ implode(',', $data['details']['meta']['total_landlord_other_subject_id']) }}
                            >
                            @foreach( $data['details']['data'] as $detail_data )
                                <div class="col-xs-6 px-5">
                                    {{ $detail_data['subject'] }}
                                    @if( $detail_data['bill_serial_number'] != '' )
                                        (帳單號:{{ $detail_data['bill_serial_number'] }})
                                    @endif
                                    @if( $detail_data['bill_start_date'] != '' )
                                        (期間: {{ $detail_data['bill_start_date'] }} ~ {{ $detail_data['bill_end_date'] }} )
                                    @endif
                                </div>
                                <div class="col-xs-2 text-center">{{ substr(strval($detail_data['paid_at']),5,5) }}</div>
                                @if( $detail_data['type'] == '收入' )
                                    <div class="col-xs-2 text-right">{{ number_format($detail_data['amount']) }}</div>
                                    <div class="col-xs-2 text-right"></div>
                                @else
                                    <div class="col-xs-2 text-right"></div>
                                    <div class="col-xs-2 text-right">{{ number_format(abs($detail_data['amount'])) }}</div>
                                @endif
                            @endforeach
                            <div class="col-xs-12 border border-dark ml-3 mb-0" style="height: 0px;"></div>
                            <div class="col-xs-6"></div>
                            <div class="col-xs-2 text-center"><span class="left-bottom">小計</span></div>
                            <div class="col-xs-2 text-right"><span class="left-bottom">{{ $data['details']['meta']['total_incomes'] }}</span></div>
                            <div class="col-xs-2 text-right"><span class="left-bottom">{{ $data['details']['meta']['total_expenses'] }}</span></div>
                    </div>
                    </div>
                    {{-- Detail data end --}}
                    {{-- Shareholder data --}}
                    @if( !empty($data['shareholders'] ))
                    <div class="col-xs-12 px-0 border border-dark">
                        <div class="col-xs-2 text-center border border-dark py-4 my-0">
                            股東分配
                        </div>
                        <div class="col-xs-10 align-self-start px-0">
                            @foreach( $data['shareholders'] as $shareholder )
                                <div class="col-xs-6 px-5">
                                    {{ $shareholder['name'] }}
                                </div>
                                <div class="col-xs-2 text-center">{{ $shareholder['current_period'] }} / {{ $shareholder['max_period'] }}</div>
                                <div class="col-xs-2 text-right"></div>
                                <div class="col-xs-2 text-right">{{ number_format($shareholder['distribution_fee']) }}</div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                    {{-- Shareholder data end --}}
                    {{-- Footer --}}
                    <div class="col-xs-12 px-0 text-right mb-0">
                        <div class="col-xs-2"></div>
                        <div class="col-xs-10 px-0">
                            <div class="col-xs-6"></div>
                            <div class="col-xs-2">合計</div>
                            <div class="col-xs-2">
                                {{ number_format($data['meta']['total_income']) }}</div>
                            <div class="col-xs-2">
                                {{ number_format($data['meta']['total_expense']) }}
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-12 px-0 text-right mb-0">
                        <div class="col-xs-2"></div>
                        <div class="col-xs-10 px-0">
                            <div class="col-xs-6"></div>
                            <div class="col-xs-2">服務費</div>
                            <div class="col-xs-2">{{ number_format($data['meta']['total_management_fee']) }}</div>
                            <div class="col-xs-2"></div>
                        </div>
                    </div>
                    <div class="col-xs-12 px-0 text-right mb-0">
                        <div class="col-xs-2"></div>
                        <div class="col-xs-10 px-0">
                            <div class="col-xs-6"></div>
                            <div class="col-xs-2">仲介費</div>
                            <div class="col-xs-2">{{ number_format($data['meta']['total_agency_fee']) }}</div>
                            <div class="col-xs-2"></div>
                        </div>
                    </div>
                    {{-- Footer end --}}
                </div>
                {{-- Electricity start --}}
                <h3 class="text-center py-4">電費報表</h3>
                <div class="row electricity-report">
                    {{-- Meta start --}}
                    <div class="col-xs-1 font-weight-bold">年度</div>
                    <div class="col-xs-1">{{ $eletricity_data['meta']['year'] }}</div>
                    <div class="col-xs-1 font-weight-bold">月度</div>
                    <div class="col-xs-1">{{ $eletricity_data['meta']['month'] }}</div>
                    <div class="col-xs-1 font-weight-bold">製表日</div>
                    <div class="col-xs-7">{{ $eletricity_data['meta']['produce_date'] }}</div>
                    {{-- Meta end --}}
                    {{-- header start --}}
                    <div class="col-xs-1 font-weight-bold">110v起</div>
                    <div class="col-xs-1 font-weight-bold">220v起</div>
                    <div class="col-xs-1 font-weight-bold">110v結</div>
                    <div class="col-xs-1 font-weight-bold">220v結</div>
                    <div class="col-xs-1 font-weight-bold">元/度</div>
                    <div class="col-xs-1 font-weight-bold">用電金額</div>
                    <div class="col-xs-1 font-weight-bold">欠額</div>
                    <div class="col-xs-1 font-weight-bold">應付金額</div>
                    <div class="col-xs-1 font-weight-bold">房號</div>
                    <div class="col-xs-1 font-weight-bold">入帳金額</div>
                    <div class="col-xs-2 font-weight-bold">繳款日</div>
                    {{-- header end --}}
                    @foreach( $eletricity_data['rooms'] as $room_data )
                        <div class="col-xs-1">{{$room_data['start_110v']}}</div>
                        <div class="col-xs-1">{{$room_data['start_220v']}}</div>
                        <div class="col-xs-1">{{$room_data['end_110v']}}</div>
                        <div class="col-xs-1">{{$room_data['end_220v']}}</div>
                        <div class="col-xs-1">{{$room_data['electricity_price_per_degree']}}</div>
                        <div class="col-xs-1">{{$room_data['current_amount']}}</div>
                        <div class="col-xs-1">{{$room_data['debt']}}</div>
                        <div class="col-xs-1">{{$room_data['should_paid']}}</div>
                        <div class="col-xs-1">{{$room_data['room_number']}}</div>
                        <div class="col-xs-1">{{$room_data['pay_log_amount']}}</div>
                        <div class="col-xs-2">{{$room_data['pay_log_date']}}</div>
                    @endforeach
                </div>
                {{-- Electricity end --}}
            </div>
        </div>
    </div>
</body>
</html>


<script src="https://cdn.jsdelivr.net/npm/html2canvas@1.0.0-rc.5/dist/html2canvas.min.js"></script>
<script src="https://unpkg.com/jspdf@latest/dist/jspdf.min.js"></script>
<script>
    window.onload = function() {
        setTimeout(function(){
            $el = document.querySelector('html')
            html2canvas($el,{ backgroundColor: null,useCORS: true})
                .then( canvas => {
                    var contentWidth = canvas.width;
                    var contentHeight = canvas.height;

                    //一页pdf显示html页面生成的canvas高度;
                    var pageHeight = contentWidth / 592.28 * 841.89;
                    //未生成pdf的html页面高度
                    var leftHeight = contentHeight;
                    //页面偏移
                    var position = 0;
                    //a4纸的尺寸[595.28,841.89]，html页面生成的canvas在pdf中图片的宽高
                    var imgWidth = 575.28;
                    var imgHeight = 572.28/contentWidth * contentHeight;

                    var pageData = canvas.toDataURL('image/png', 1.0);
                    // document.write('<img src="'+pageData+'"/>');

                    var pdf = new jsPDF('', 'pt', 'a4');
                    //有两个高度需要区分，一个是html页面的实际高度，和生成pdf的页面高度(841.89)
                    //当内容未超过pdf一页显示的范围，无需分页
                    if (leftHeight < pageHeight) {
                        pdf.addImage(pageData, 'JPEG', 10, 0, imgWidth, imgHeight );
                    } else {
                        while(leftHeight > 0) {
                            pdf.addImage(pageData, 'JPEG', 10, position, imgWidth, imgHeight)
                            leftHeight -= pageHeight;
                            position -= 841.89;
                            //避免添加空白页
                            if(leftHeight > 0) {
                                pdf.addPage();
                            }
                        }
                    }
                    pdf.save("{{ $file_name }}");
                })
        },3000)
    };
</script>
