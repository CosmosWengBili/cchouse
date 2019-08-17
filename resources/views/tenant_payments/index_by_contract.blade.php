@extends('layouts.app')
@section('content')
<input id="csrf" type="hidden" value={{ csrf_token() }}>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12 my-4">
            <ul class="nav nav-tabs">
                <li class="nav-item">
                    <a
                        class="nav-link {{ $by == 'contract' ? 'active' : '' }}"
                        href="{{ route('tenantPayments.index', ['by' => 'contract']) }}"
                    >
                        By Contract
                    </a>
                </li>
                <li class="nav-item">
                    <a
                        class="nav-link {{ $by == 'time' ? 'active' : '' }}"
                        href="{{ route('tenantPayments.index', ['by' => 'time']) }}"
                    >
                        By Time
                    </a>
                </li>
            </ul>

            {{-- for showing multiple types of entries returned --}}
            @foreach ( $data as $type => $entries)
                @include('tenant_payments.table', ['objects' => $entries, 'layer' => $type])
            @endforeach
        </div>
    </div>
</div>
@endsection
