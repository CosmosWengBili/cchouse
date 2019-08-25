@extends('layouts.app')
@section('content')

<div class="container">
    <div class="row justify-content-center my-3">
        <div class="col-8 card">
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
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <th>110v 電費度數</th>
                            <td colspan="2">{{ $payOffData['110v_end_degree'] }} 度</td>
                        </tr>
                        <tr>
                            <th>220v 電費度數</th>
                            <td colspan="2">{{ $payOffData['220v_end_degree'] }} 度</td>
                        </tr>
                        <tr>
                            <th>科目</th>
                            <th>費用</th>
                            <th>備註</th>
                        </tr>
                        @foreach($payOffData['fees'] as $fee)
                            <tr>
                                <td>{{ $fee['subject'] }}</td>
                                <td>
                                    @php
                                        $refundAmount += $fee['amount'];
                                    @endphp
                                    {{ $fee['amount'] }}  元
                                </td>
                                <td>{{ $fee['comment'] }}</td>
                            </tr>
                        @endforeach
                        <tr>
                            <th>應退金額</th>
                            <td>{{ $refundAmount }}</td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            @else
                <h3 class="text-center my-5">請選擇上方日期選擇器產生報表</h3>
            @endif
        </div>
        </div>
</div>
@endsection
