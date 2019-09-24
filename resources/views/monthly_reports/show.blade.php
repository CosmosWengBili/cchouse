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
    left: 40%;
}
.monthly-report .add-subject{
    position: absolute;
    top: -10%;
    left: 95%
}
.monthly-report .delete-subject{
    position: absolute;
    top: -10%;
    left: 140%
}
</style>
<div class="container-fluid">
    <div class="card">
        <div class="card-body table-responsive" style="padding: 5rem;">
            <a href="{{route('monthlyReports.print', $data['building_id'])}}?month={{$report_used_date['month']}}&year={{$report_used_date['year']}}">
                輸出為 PDF
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
                <div class="col-8">{{implode(",", $data['meta']['building_code']->toArray())}}</div>
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
                <div class="col-6 text-center border-top border-dark">租約起迄日及租金條件</div>
                <div class="col-6 text-center border-top border-dark">入帳月份及收入支出金額</div>
                <div class="col-12 row px-0 text-center bg-gray mb-0">
                    <div class="col-2"></div>
                    <div class="col-10 row px-0">
                        <div class="col-8"></div>
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
                        <div class="col-8 px-5">
                            <input class="w-100 landlord-other-subject">
                        </div>
                        <div class="col-2 text-center"><input class="w-100 landlord-other-date" type="date"></div>
                        <div class="col-1 text-center"><input class="w-100 landlord-other-income"></div>
                        <div class="col-1 text-center position-relative">
                            <input class="w-100 landlord-other-expense">
                            <button class="btn btn-sm btn-success rounded-pill add-subject">+</button>
                            <button class="btn btn-sm btn-danger rounded-pill delete-subject">-</button>
                        </div>
                   </div>
                </div>                
                {{-- Detail data end --}}
                {{-- Shareholder data --}}
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
                        <div class="col-2">本公司服務費</div>
                        <div class="col-1">{{$data['meta']['total_management_fee']}}</div>
                        <div class="col-1"></div>
                    </div>
                </div>  
                <div class="col-12 row px-0 text-center mb-0">
                    <div class="col-2"></div>
                    <div class="col-10 row px-0">
                        <div class="col-8"></div>
                        <div class="col-2">仲介費合計</div>
                        <div class="col-1">{{$data['meta']['total_agency_fee']}}</div>
                        <div class="col-1"></div>
                    </div>
                </div>    
                <div class="col-3">
                    <button id="save-other-subjects" class="btn btn-block btn-success">儲存</button>
                </div>                          
                {{-- Footer end --}}
            </div>
        </div>
    </div>
</div>
<script>
    $('body').on('click', '.add-subject', function(){
        const $buttons = $(this).parent().find('button')
        $buttons.remove()

        const element = 
        '<div class="col-8 px-5">' +
        '   <input class="w-100 landlord-other-subject">' +
        '</div>' +
        '<div class="col-2 text-center"><input class="w-100 landlord-other-date" type="date"></div>' +
        '<div class="col-1 text-center"><input class="w-100 landlord-other-income"></div>' +
        '<div class="col-1 text-center position-relative">' +
        '   <input class="w-100 landlord-other-expense">' +
        '   <button class="btn btn-sm btn-success rounded-pill add-subject">+</button>'  +
        '   <button class="btn btn-sm btn-danger rounded-pill delete-subject">-</button>' +
        '</div>'
        
        $('#detail-data').append(element)
    })
    $('body').on('click', '.delete-subject', function(){
        const element =
        '   <button class="btn btn-sm btn-success rounded-pill add-subject">+</button>'  +
        '   <button class="btn btn-sm btn-danger rounded-pill delete-subject">-</button>'

        // delete one by one bacause of element structure
        $(this).parent().prev().prev().prev().prev().append(element)
        $(this).parent().prev().prev().prev().remove()
        $(this).parent().prev().prev().remove()
        $(this).parent().prev().remove()
        $(this).parent().remove()
    })

    $('#save-other-subjects').on('click', function(){
        const apiURL = '{{ route('monthlyReports.storeOtherSubjects', $data['building_id']) }}';

        // add new landlord other subject data
        const addedData = $.map($('.landlord-other-subject'), function(subject, index){
            var tmpData = {
                'subject' : subject.value,
                'date' : $('.landlord-other-date')[index].value,
                'income' : $('.landlord-other-income')[index].value,
                'expense' : $('.landlord-other-expense')[index].value,
            }
            return tmpData;
        })

        // delete deleted subjects
        const totalIds = $('#total_landlord_other_subject_id').val().split(',')
        const keepIds = $.map($('.delete-real-subject'), function(subject, index){
            return subject.dataset.id
        })
        const deleteIds = totalIds.filter(x => !keepIds.includes(x));

        $.post(apiURL, { data: addedData, deleteIds:  deleteIds}, function (data) {
            location.reload();
        })
    })

    // delete one by one bacause of element structure
    $('.delete-real-subject').on('click', function(){
        $(this).parent().next().next().next().remove()
        $(this).parent().next().next().remove()
        $(this).parent().next().remove()
        $(this).parent().remove()
    })
</script>
@endsection