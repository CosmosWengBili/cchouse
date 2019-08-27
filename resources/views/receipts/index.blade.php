@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12 mt-4">
            <ul class="nav nav-tabs justify-content-center" id="type-tabs" role="tablist">
                <li class="nav-item {{ $type == "invoice" ? 'active' : ''  }}">
                        <a
                        class="nav-link {{ $type == "invoice"  ? 'active' : ''  }}"
                        data-toggle="tab"
                        href="#invoice-pane"
                        role="tab"
                        >發票報表</a>
                </li>
                <li class="nav-item {{ $type == "receipt" ? 'active' : ''  }}">
                        <a
                        class="nav-link {{ $type == "receipt"  ? 'active' : ''  }}"
                        data-toggle="tab"
                        href="#receipt-pane"
                        role="tab"
                        >收據報表</a>
                </li>
            </ul>
            <div class="tab-content pt-0">
                <div class="tab-pane fade {{ $type == "invoice" ? 'show active' : ''  }}" id="invoice-pane" role="tabpanel">
                    @include('receipts.invoice_table', ['objects' => $invoiceData])
                </div>
                <div class="tab-pane fade {{ $type == "receipt" ? 'show active' : ''  }}" id="receipt-pane" role="tabpanel">
                    @include('receipts.receipt_table', ['objects' => $receiptData])
                </div>
            </div>
        </div>
        <div class="col-md-12 mt-4">
            {{-- for showing multiple types of entries returned --}}
            @foreach ( $data as $type => $entries)
                @include('receipts.table', ['objects' => $entries, 'layer' => $type])
            @endforeach
        </div>
    </div>
</div>
@endsection
