@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-12 mt-4">
        <div class="card">
            <div class="card-body table-responsive">
                <h2>
                    發票更新
                </h2>
                <form action="edit_invoice" method="GET">
                    開始日期 <input type="date" name="start_date">
                    結束日期 <input type="date" name="end_date">
                    <input class="btn btn-primary"  type="submit" value="送出">
                </form>

                {{-- you should handle the empty array logic --}}
                @if (empty($invoiceData))
                    <h3>查無紀錄</h3>
                @else
                    <form data-target="#invoice-table" data-toggle="datatable-query">
                        <div class="query-box">
                        </div>
                        <i class="fa fa-plus-circle" data-toggle="datatable-query-add"></i>
                        <input type="submit" class="btn btn-sm btn-primary" value="搜尋">
                    </form>
                    <form action="update_invoice" method="POST">
                        @csrf
                        <table id="invoice-table" class="display table" style="width:100%">
                            <thead>
                                @foreach(config('enums.invoice') as $invoice_column)
                                    <th>{{$invoice_column}}</th>
                                @endforeach
                            </thead>
                            <tbody>
                                {{-- all the records --}}
                                @foreach ( $invoiceData as $data_idx => $object )
                                    <tr>
                                        <td>{{ $object['invoice_count']}}</td>
                                        <td>
                                            <input type="hidden" name="receipts[{{$object['data_table']}}][{{$data_idx}}][{{$object['data_table_id']}}][invoice_date]" value="{{ $object['invoice_date']}}">
                                            {{ $object['invoice_date']}}
                                        </td>
                                        <td>{{ $object['invoice_item_idx']}}</td>
                                        <td>{{ $object['invoice_item_name']}}</td>
                                        <td>{{ $object['quantity']}}</td>
                                        <td>{{ $object['amount']}}</td>
                                        <td>{{ $object['tax_type']}}</td>
                                        <td>{{ $object['tax_rate']}}</td>
                                        <td>{{ $object['data_table']}}</td>
                                        <td>{{ $object['data_table_id']}}</td>
                                        <td>{{ $object['company_number']}}</td>
                                        <td>{{ $object['company_name']}}</td>
                                        <td>{{ $object['room_code']}}</td>
                                        <td>{{ $object['room_number']}}</td>
                                        <td>{{ $object['deposit_date']}}</td>
                                        <td>{{ $object['actual_deposit_date']}}</td>
                                        <td>{{ $object['invoice_collection_number']}}</td>
                                        <td>
                                            <input type="text" name="receipts[{{$object['data_table']}}][{{$data_idx}}][{{$object['data_table_id']}}][invoice_serial_number]" value="{{$object['invoice_serial_number']}}">
                                        </td>
                                        <td>
                                            <input type="hidden" name="receipts[{{$object['data_table']}}][{{$data_idx}}][{{$object['data_table_id']}}][receipt_id]" value="{{ $object['data_receipt_id']}}">
                                            <input type="hidden" name="receipts[{{$object['data_table']}}][{{$data_idx}}][{{$object['data_table_id']}}][invoice_price]" value="{{ $object['invoice_price']}}">
                                            {{ $object['invoice_price']}}
                                        </td>
                                        <td>{{ $object['comment']}}</td>
                                        <td>{{ $object['subtotal']}}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="w-25 mx-auto">
                            <input type="submit" class="btn btn-block btn-success" value="更新發票號碼">
                        </div>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
    renderDataTable(["#invoice-table"], {
        'pageLength' : 500
    });
</script>
@endsection