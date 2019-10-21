@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col">
            @include('pay_logs.tabs', ['by' => 'date'])
            <div class="card">
                <div class="card-body">
                    <form class="my-3" method="get">
                        <input type="hidden" name="by" value="date" />
                        <div class="form-row">
                            <div class="col">
                                <input type="date" name="start_date" class="form-control form-control-sm" value="{{ Request::get('start_date') ?? '' }}" required>
                            </div>
                            <div class="col" style="flex: 0 0;">
                                <span class="font-weight-bold" style="line-height: 41px;">至</span>
                            </div>
                            <div class="col">
                                <input type="date" name="end_date" class="form-control form-control-sm" value="{{ Request::get('end_date') ?? '' }}" required>
                            </div>
                            <div class="col">
                                <button class="btn btn-success btn-sm m-1" type="submit">查詢</button>
                            </div>
                        </div>

                    </form>

                    @if(isset($tableRows))
                        @php
                            $remain = 0;
                        @endphp
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                <tr>
                                    <th>繳費科目</th>
                                    <th>費用</th>
                                    <th>繳費虛擬帳號</th>
                                    <th>入帳日期</th>
                                </tr>
                                </thead>
                                <tbody>
                                    @forelse($tableRows as $row)
                                        <tr>
                                            <td>{{ $row['繳費科目'] ?? '' }}</td>
                                            <td>
                                                @php
                                                    $pay = $row['繳費費用'] ?? '';
                                                    $remain -= $pay == '' ? 0 : $pay;
                                                @endphp
                                                {{ $pay }}
                                            </td>
                                            <td>{{ $row['繳費虛擬帳號'] ?? '' }}</td>
                                            <td>{{ $row['繳費日期'] ?? '' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="text-center">查無資料</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    @else
                        <h2 class="text-center my-5">請選擇查詢區間</h2>
                    @endif
                    <p class="h2 my-3">總額： ${{$total}}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
