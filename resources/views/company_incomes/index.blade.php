@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col">
            <ul class="nav nav-tabs justify-content-center" role="tablist">
                @for($i = 5; $i >= 0; $i--)
                    @php
                        $current = \Carbon\Carbon::now()->copy()->subMonth($i)
                    @endphp
                    <li class="nav-item">
                        <a
                            class="nav-link {{ $i == 5 ? 'active' : ''  }}"
                            data-toggle="tab"
                            href="#pane-{{ $current->year }}-{{ $current->month }}"
                            role="tab"
                        >
                           {{ $current->year }}年 {{ $current->month }}月
                        </a>
                    </li>
                @endfor
            </ul>
            <div class="tab-content pt-0">
                @for($i = 5; $i >= 0; $i--)
                    @php
                        $current = \Carbon\Carbon::now()->copy()->subMonth($i);
                        $entries = $companyIncomes[$current->month] ?? [];
                        $type = 'company_incomes';
                        $model_name = 'CompanyIncome';
                        $total = 0
                    @endphp
                    @foreach( $entries as $entry )
                        @php 
                            $total += $entry['amount']
                        @endphp
                    @endforeach
                    <div class="tab-pane fade {{ $i == 5 ? 'show active' : ''  }}" id="pane-{{ $current->year }}-{{ $current->month }}" role="tabpanel">
                        <div class="card">
                            @if(count($entries) > 0)
                                @include('company_incomes.table', ['objects' => $entries, 'layer' => $type])
                                <div class="my-3 mx-3 h3">總計： <span class="countAmount">{{$total}}</span>元</div>
                            @else
                                <div class="text-center h3 my-5 py-5">尚無紀錄</div>
                            @endif
                        </div>
                    </div>
                @endfor
            </div>
        </div>
    </div>
</div>
@endsection

