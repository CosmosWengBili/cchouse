@extends('layouts.app')
@section('content')
<input id="csrf" type="hidden" value={{ csrf_token() }}>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12 my-4">
            @include('tenant_payments.tabs', ['by' => 'date'])
            <div class="card">
                <div class="card-body">
                    <form class="my-3" method="get">
                        <input type="hidden" name="by" value="date" />
                        <div class="form-row">
                            <div class="col">
                                <input type="text" class="form-control form-control-sm" name="room_code" placeholder="物件代碼" value="{{ Request::get('room_code') ?? '' }}" required>
                            </div>
                            <div class="col">
                                <input type="text" class="form-control form-control-sm" name="tenant_name" placeholder="租客姓名" value="{{ Request::get('tenant_name') ?? '' }}" required>
                            </div>
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
                                    <th>應繳費用</th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th>繳費記錄</th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                </tr>
                                <tr>
                                    <th>科目類別</th>
                                    <th>費用</th>
                                    <th>應繳日期</th>
                                    <th>是否已沖銷</th>
                                    <th>繳費科目</th>
                                    <th>費用</th>
                                    <th>應繳日期</th>
                                    <th>入帳日期</th>
                                </tr>
                                </thead>
                                <tbody>
                                    @forelse($tableRows as $row)
                                        <tr>
                                            <td>{{ $row['應繳科目'] ?? '' }}</td>
                                            <td>
                                                @php
                                                    $fee = $row['應繳費用'] ?? '';
                                                    $remain += $fee == '' ? 0 : $fee;
                                                @endphp
                                                {{ $fee }}
                                            </td>
                                            <td>{{ $row['應繳日期'] ?? '' }}</td>
                                            <td>{{ ($row['是否已沖銷'] ?? false) ? 'V' : '' }}</td>
                                            <td>{{ $row['繳費科目'] ?? '' }}</td>
                                            <td>
                                                @php
                                                    $pay = $row['繳費費用'] ?? '';
                                                    $remain -= $pay == '' ? 0 : $pay;
                                                @endphp
                                                {{ $pay }}
                                            </td>
                                            <td>{{ $row['繳費應繳日期'] ?? '' }}</td>
                                            <td>{{ $row['繳費日期'] ?? '' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="text-center">查無資料</td>
                                        </tr>
                                    @endforelse
                                    <tr>
                                        <th>餘額:</th>
                                        <td colspan="2">{{ $remain }} (查看當日的餘額)</td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    @else
                        <h2 class="text-center my-5">請選擇查詢區間</h2>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
