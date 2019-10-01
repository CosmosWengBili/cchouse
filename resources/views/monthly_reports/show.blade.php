@extends('layouts.app')
@section('content')
<style>
.monthly-report div{
    margin-top: 0.2rem;
    margin-bottom: 0.25rem;
}
.monthly-report .room > div{
    min-height: 100px;
}
.monthly-report .room span.left-bottom{
    position: absolute;
    bottom: 0px;
    left: 42%;
}
.electricity-report div{
    border: 1px solid gray;
}
</style>
<div class="container-fluid">
    <div class="card">
        <div class="card-body table-responsive" style="padding: 5rem;">
            <a href="{{route('monthlyReports.print', $data['building_id'])}}?month={{$report_used_date['month']}}&year={{$report_used_date['year']}}">
                輸出為 PDF
            </a> |
            <a href="{{route('monthlyReports.print_tenant', $data['building_id'])}}?month={{$report_used_date['month']}}&year={{$report_used_date['year']}}">
                輸出租客報表
            </a>
            @include('monthly_reports.tabs', ['by' => 'contract'])
            <div class="row justify-content-center monthly-report mt-3">
                {{-- Header --}}
                <div class="col-3">
                    <img src="/images/monthly_report_logo.png" style="width: 100%;">
                </div>
                <div class="col-6 text-center">
                    <h2>綜合月結單</h2>
                </div>
                <div class="col-3"></div>
                <div class="col-12 text-right border-bottom border-dark">
                    <p class="mr-4">
                        {{$report_used_date['year'] - 1911}}年 <span class="bg-highlight px-3 text-center">{{$report_used_date['month']}}</span>月份
                    </p>   
                </div>
                {{-- Header end --}}
                {{-- Meta data --}}
                <div class="col-1">客戶姓名</div>
                <div class="col-3">{{implode(",", $data['meta']['landlord_name']->toArray())}}</div>
                <div class="col-1">管理期間</div>
                <div class="col-4">{{$data['meta']['period']}}</div>
                <div class="col-3"></div>

                <div class="col-1">物件代碼</div>
                <div class="col-8">{{$data['meta']['building_code']}}</div>
                <div class="col-1 bg-gray">本月收入</div>
                <div class="col-2">{{$data['meta']['total_income']}}</div>

                <div class="col-1">物件地址</div>
                <div class="col-11">{{$data['meta']['building_location']}}</div>

                <div class="col-1">管理戶數</div>
                <div class="col-3">{{$data['meta']['rooms_count']}}</div>
                <div class="col-1">聯絡電話</div>
                <div class="col-4">{{implode(",", $data['meta']['landlords_phones']->toArray())}}</div>
                <div class="col-1 bg-gray">本月支出</div>
                <div class="col-2">{{$data['meta']['total_expense']}}</div>

                <div class="col-12">&nbsp;</div>

                <div class="col-1">匯款帳號</div>
                <div class="col-8">{{implode(",", $data['meta']['account_numbers'])}}</div>
                <div class="col-1 bg-gray">
                    @if( $data['meta']['total_income'] - $data['meta']['total_expense'] > 0 )
                        本月實收
                    @else
                        本月應付
                    @endif
                </div>
                <div class="col-2">{{ $data['meta']['total_income'] - $data['meta']['total_expense']}}</div>

                <div class="col-12">&nbsp;</div>

                <div class="col-2">帳單郵寄或傳真</div>
                <div class="col-7">{{implode(",", $data['meta']['account_address'])}}</div>
                <div class="col-1 px-1 text-center bg-gray">約定入帳日</div>
                <div class="col-2 bg-highlight px-3 text-center">{{$report_used_date['next_month']}}月{{$data['meta']['rent_collection_time']}}日</div>
                {{-- Meta data end --}}
                {{-- Room data --}}
                <div class="col-12 row px-0 text-center bg-gray mb-0">
                    <div class="col-2"></div>
                    <div class="col-10 row px-0">
                        <div class="col-1"></div>
                        <div class="col-7 text-left">說明</div>
                        <div class="col-2">入帳日</div>
                        <div class="col-1">收入</div>
                        <div class="col-1">支出</div>
                    </div>
                </div>
                @foreach( $data['rooms'] as $room )
                <div class="col-12 row px-0 room border border-dark">
                    <div class="col-2 text-center border border-dark py-5 my-0">
                        {{$room['meta']['room_number']}}室<br/>
                        @if( $room['meta']['management_fee_mode'] == "比例" )
                            (服務費率 {{ $room['meta']['management_fee'] }} %)
                        @elseif( $room['meta']['management_fee_mode'] == "固定" )
                            (服務費 {{ $room['meta']['management_fee'] }})
                        @endif
                        <br/>
                        狀態:  {{ $room['meta']['status'] }}
                    </div>
                    <div class="col-10 row px-0">
                        @php
                            $income_not_zero = count($room['incomes']) > 0;
                            $expense_not_zero = count($room['expenses']) > 0;
                        @endphp
                        @foreach( $room['incomes'] as $income )
                            <div class="col-8 px-5">{{ $income['subject'] }}( {{ $income['month'] }} )</div>
                            <div class="col-2 text-center">{{ $income['paid_at']->format('m-d') }}</div>
                            <div class="col-1 text-center">{{ $income['amount'] }}</div>
                            <div class="col-1 text-center"></div>
                        @endforeach
                        @if( $income_not_zero )
                            <div class="col-12 border border-dark ml-3 mb-0" style="height: 0px;"></div>
                        @endif
                        @foreach( $room['expenses'] as $expense )
                            <div class="col-8 px-5">{{ $expense['subject'] }}</div>
                            <div class="col-2 text-center">{{ $expense['paid_at']->format('m-d') }}</div>
                            <div class="col-1 text-center"></div>
                            <div class="col-1 text-center">{{ $expense['amount'] }}</div>
                        @endforeach
                        @if( $expense_not_zero )
                            <div class="col-12 border border-dark ml-3 mb-0" style="height: 0px;"></div>
                        @endif
                        <div class="col-8"></div>
                        <div class="col-2 text-center"><span class="{{ ($expense_not_zero && $income_not_zero) == false ? 'left-bottom' : ''  }}">小計</span></div>
                        <div class="col-1 text-center"><span class="{{ ($expense_not_zero && $income_not_zero) == false ? 'left-bottom' : ''  }}">{{ $room['meta']['room_total_income'] }}</span></div>
                        <div class="col-1 text-center"><span class="{{ ($expense_not_zero && $income_not_zero) == false ? 'left-bottom' : ''  }}">{{ $room['meta']['room_total_expense'] }}</span></div>
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
                <div class="col-12 row px-0 room border border-dark">
                    <div class="col-2 text-center border border-dark py-5 my-0">
                        {{$payoff['meta']['room_number']}}室(點交)<br/>
                    </div>
                    <div class="col-10 row px-0">
                        @foreach( $payoff['incomes'] as $income )
                            <div class="col-8 px-5">{{ $income['subject'] }}( {{ $income['month'] }} )</div>
                            <div class="col-2 text-center">{{ $income['paid_at']->format('m-d') }}</div>
                            <div class="col-1 text-center">{{ $income['amount'] }}</div>
                            <div class="col-1 text-center"></div>
                        @endforeach
                        @if( count($payoff['incomes']) > 0 )
                            <div class="col-12 border border-dark ml-3 mb-0" style="height: 0px;"></div>
                        @endif

                        @foreach( $payoff['expenses'] as $expense )
                            <div class="col-8 px-5">{{ $expense['subject'] }}</div>
                            <div class="col-2 text-center">{{ $expense['paid_at']->format('m-d') }}</div>
                            <div class="col-1 text-center"></div>
                            <div class="col-1 text-center">{{ $expense['amount'] }}</div>
                        @endforeach
                        @if( count($payoff['expenses']) > 0 )
                            <div class="col-12 border border-dark ml-3 mb-0" style="height: 0px;"></div>
                        @endif
                        <div class="col-8"></div>
                        <div class="col-2 text-center"><span class="{{ ($expense_not_zero && $income_not_zero) == false ? 'left-bottom' : ''  }}">小計</span></div>
                        <div class="col-1 text-center"><span class="{{ ($expense_not_zero && $income_not_zero) == false ? 'left-bottom' : ''  }}">{{ $payoff['meta']['room_total_income'] }}</span></div>
                        <div class="col-1 text-center"><span class="{{ ($expense_not_zero && $income_not_zero) == false ? 'left-bottom' : ''  }}">{{ $payoff['meta']['room_total_expense'] }}</span></div>
                   </div>
                </div>
                @endforeach                
                {{-- PayOff data end --}}
                {{-- Detail data --}}
                <div class="col-12 row px-0 border border-dark">
                    <div class="col-2 text-center border border-dark py-5 my-0">
                        費用明細
                    </div>
                    <div id="detail-data" class="col-10 align-self-start row px-0">
                        <input type="hidden" 
                               id="total_landlord_other_subject_id" 
                               value={{ implode(',', $data['details']['meta']['total_landlord_other_subject_id']) }}
                        >
                        @foreach( $data['details']['data'] as $detail_data )
                            <div class="col-8 px-5">
                                @if( array_key_exists('landlord_other_subject_id', $detail_data) )
                                    <span class="badge badge-pill badge-danger delete-real-subject" 
                                          style="cursor:pointer"
                                          data-id={{ $detail_data['landlord_other_subject_id'] }}>-</span>
                                @endif                                
                                {{ $detail_data['subject'] }}
                                @if( $detail_data['bill_serial_number'] != '' )
                                    (帳單號:{{ $detail_data['bill_serial_number'] }})
                                @endif
                                @if( $detail_data['bill_start_date'] != '' )
                                    (期間: {{ $detail_data['bill_start_date'] }} ~ {{ $detail_data['bill_end_date'] }} )
                                @endif
                            </div>
                            <div class="col-2 text-center">{{ substr(strval($detail_data['paid_at']),5,5) }}</div>
                            @if( $detail_data['type'] == '收入' )
                                <div class="col-1 text-center">{{ $detail_data['amount'] }}</div>
                                <div class="col-1 text-center"></div>
                            @else
                                <div class="col-1 text-center"></div>
                                <div class="col-1 text-center">{{ abs($detail_data['amount']) }}</div>
                            @endif                          
                        @endforeach
                        <div class="col-12 border border-dark ml-3 mb-0" style="height: 0px;"></div>
                        <div class="col-8"></div>
                        <div class="col-2 text-center"><span >小計</span></div>
                        <div class="col-1 text-center"><span>{{ $data['details']['meta']['total_incomes'] }}</span></div>
                        <div class="col-1 text-center"><span>{{ $data['details']['meta']['total_expenses'] }}</span></div>  
                   </div>
                </div>                
                {{-- Detail data end --}}
                {{-- Shareholder data --}}
                @if( !empty($data['shareholders'] ))
                <div class="col-12 row px-0 border border-dark">
                    <div class="col-2 text-center border border-dark py-4 my-0">
                        股東分配
                    </div>
                    <div class="col-10 align-self-start row px-0">
                        @foreach( $data['shareholders'] as $shareholder )
                            <div class="col-8 px-5">
                                {{ $shareholder['name'] }}
                            </div>
                            <div class="col-2 text-center">{{ $shareholder['current_period'] }} / {{ $shareholder['max_period'] }}</div>
                            <div class="col-1 text-center"></div>
                            <div class="col-1 text-center">{{ $shareholder['distribution_fee'] }}</div>
                        @endforeach
                    </div>
                </div>
                @endif                
                {{-- Shareholder data end --}}
                {{-- Footer --}}
                <div class="col-12 row px-0 text-center mb-0">
                    <div class="col-2"></div>
                    <div class="col-10 row px-0">
                        <div class="col-8"></div>
                        <div class="col-2">合計</div>
                        <div class="col-1">{{$data['meta']['total_income']}}</div>
                        <div class="col-1">{{$data['meta']['total_expense']}}</div>
                    </div>
                </div>  
                <div class="col-12 row px-0 text-center mb-0">
                    <div class="col-2"></div>
                    <div class="col-10 row px-0">
                        <div class="col-8"></div>
                        <div class="col-2">服務費</div>
                        <div class="col-1">{{$data['meta']['total_management_fee']}}</div>
                        <div class="col-1"></div>
                    </div>
                </div>  
                <div class="col-12 row px-0 text-center mb-0">
                    <div class="col-2"></div>
                    <div class="col-10 row px-0">
                        <div class="col-8"></div>
                        <div class="col-2">仲介費</div>
                        <div class="col-1">{{$data['meta']['total_agency_fee']}}</div>
                        <div class="col-1"></div>
                    </div>
                </div>                            
                {{-- Footer end --}}
            </div>
            <h2 class="text-center py-4">電費報表</h2>
            <div class="row electricity-report">
                {{-- Electricity start --}}
                {{-- Meta start --}}
                <div class="col-1 font-weight-bold">年度</div>
                <div class="col-1">{{ $eletricity_data['meta']['year'] }}</div>
                <div class="col-1 font-weight-bold">月度</div>
                <div class="col-1">{{ $eletricity_data['meta']['month'] }}</div>
                <div class="col-1 font-weight-bold">製表日</div>
                <div class="col-2">{{ $eletricity_data['meta']['produce_date'] }}</div>
                <div class="col-5"> </div>
                {{-- Meta end --}}
                {{-- header start --}}
                <div class="col-1 font-weight-bold">110v起</div>
                <div class="col-1 font-weight-bold">220v起</div>
                <div class="col-1 font-weight-bold">110v結</div>
                <div class="col-1 font-weight-bold">220v結</div>
                <div class="col-1 font-weight-bold">元/度</div>
                <div class="col-1 font-weight-bold">用電金額</div>
                <div class="col-1 font-weight-bold">欠額</div>
                <div class="col-1 font-weight-bold">應付金額</div>
                <div class="col-1 font-weight-bold">房號</div>
                <div class="col-1 font-weight-bold">入帳金額</div>
                <div class="col-2 font-weight-bold">繳款日</div>
                {{-- header end --}}
                @foreach( $eletricity_data['rooms'] as $room_data )
                    <div class="col-1">{{$room_data['start_110v']}}</div>
                    <div class="col-1">{{$room_data['start_220v']}}</div>
                    <div class="col-1">{{$room_data['end_110v']}}</div>
                    <div class="col-1">{{$room_data['end_220v']}}</div>
                    <div class="col-1">{{$room_data['electricity_price_per_degree']}}</div>
                    <div class="col-1">{{$room_data['current_amount']}}</div>
                    <div class="col-1">{{$room_data['debt']}}</div>
                    <div class="col-1">{{$room_data['should_paid']}}</div>
                    <div class="col-1">{{$room_data['room_number']}}</div>
                    <div class="col-1">{{$room_data['pay_log_amount']}}</div>
                    <div class="col-2">{{$room_data['pay_log_date']}}</div>                    
                @endforeach
                {{-- Electricity end --}}
            </div>
        </div>
    </div>
</div>
@endsection