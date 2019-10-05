<div class="card">
    <div class="card-body table-responsive">
        <h2>
            發票報表 
            <a class="btn btn-sm btn-success" href="{{ route( 'receipts.edit_invoice') }}">更新發票</a>
            <a class="btn btn-sm btn-success" href="export/function/{{$type}}/?start_date={{$start_date}}&end_date={{$end_date}}">輸出報表</a>
        </h2>
        <form action="/receipts" meth="GET">
            <input type="hidden" name="type" value="invoice">
            開始日期 <input type="date" name="start_date">
            結束日期 <input type="date" name="end_date">
            <input class="btn btn-primary"  type="submit" value="送出">
        </form>

        {{-- you should handle the empty array logic --}}
        @if (empty($objects))
            <h3>查無紀錄</h3>
        @else
            @component('layouts.tab')
                @slot('relation_titles')
                    @foreach($objects as $invoiceKey => $object)
                        <li class="nav-item {{ $loop->first ? 'active' : ''  }}">
                            <a
                                class="nav-link {{ $loop->first ? 'active' : ''  }}"
                                data-toggle="tab"
                                href="#{{ $invoiceKey }}-pane"
                                role="tab"
                            >
                                @lang("general.{$invoiceKey}")
                            </a>
                        </li>
                    @endforeach
                @endslot
                @slot('relation_contents')
                    @foreach($objects as $invoiceKey => $object)
                        @php
                            $active = $loop->first ? 'show active' : 'fade';
                        @endphp
                        <div class="tab-pane container {{ $active }}" id="{{$invoiceKey}}-pane">
                            <form data-target="#invoice-table-{{$invoiceKey}}" data-toggle="datatable-query">
                                <div class="query-box">
                                </div>
                                <i class="fa fa-plus-circle" data-toggle="datatable-query-add"></i>
                                <input type="submit" class="btn btn-sm btn-primary" value="搜尋">
                            </form>

                            <table id="invoice-table-{{$invoiceKey}}" class="display table" style="width:100%">
                                <thead>
                                    @foreach(config('enums.invoice') as $invoice_column)
                                        <th>{{$invoice_column}}</th>
                                    @endforeach
                                </thead>
                                <tbody>
                                    {{-- all the records --}}
                                    @foreach ( $object as $row )
                                        <tr>
                                            @foreach(config('enums.invoice_en') as $invoice_column_en)
                                                <td>{{ $row[$invoice_column_en]}}</td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endforeach
                @endslot
            @endcomponent
        @endif
    </div>
</div>
<script>
    @foreach($objects as $invoiceKey => $object)
        renderDataTable(["#invoice-table-{{$invoiceKey}}"], 
            { "order": [],
               "drawCallback": function( settings ) {
                    $(".dataTables_wrapper").addClass('table-responsive');
                }
            }
        );
    @endforeach
</script>
