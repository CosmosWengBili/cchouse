@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-body">
                    <h3>租客電費報表</h3>
                    <div>
                        <span class="d-inline-block mr-3">年度：{{ $year }}</span>
                        <span class="d-inline-block mr-3" >月度：{{ $month }} </span>
                        <span class="d-inline-block mr-3">製表日：{{ \Carbon\Carbon::now()->format('Y/m/d') }}</span>
                    </div>
                    @php
                        $total = 0;
                    @endphp
                    @forelse($reportRows as $reportRow)
                        <div class="table-responsive my-3">
                            <table class="table table-bordered" style="table-layout: fixed;">
                                <thead>
                                <tr>
                                    @foreach($reportRow as $header => $value)
                                        <th style="width: 135px;">{{$header}}</th>
                                    @endforeach
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    @foreach($reportRow as $header => $value)
                                        @php
                                            $total += ($header == '本期應付金額') ? $value : 0;
                                        @endphp
                                        <td>@include('shared.helpers.value_helper', ['value' => $value])</td>
                                    @endforeach
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    @empty
                        <h4 class="text-center">查無應繳電費資料</h4>
                    @endforelse

                    @if(count($reportRows) > 0)
                        <p class="h3">費用總額： {{$total}} 元</p>
                        <hr />
                        <p class="help-block">
                            @php
                                $startYear = $month - 3 <= 0 ? $year - 1 : $year;
                                $startMonth = (($month + 12) - 3) % 12;
                            @endphp
                            這是 {{ $startYear }}年{{ $startMonth }}月~{{ $year }}年{{ $month }}月 抄表結算的電費，請您核對繳款金額，<br />
                            並於下個繳租日前完成繳納，溢繳的預收電費將於下期費用扣抵結算，謝謝。<br />
                            如有疑問請電話聯繫我們。
                            <p class="h3">※ 網路為免費提供，請勿私裝分享器，如斷線不負立即修復之責任</p>
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
