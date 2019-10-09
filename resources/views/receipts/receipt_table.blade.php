<div class="card">
    <div class="card-body table-responsive">
        <h2>
            收據報表
            <a class="btn btn-sm btn-success" href="export/function/{{$type}}/?receipt_year={{$receipt_year}}&receipt_month={{$receipt_month}}">輸出報表</a>
        </h2>
        <form action="/receipts" meth="GET">
            <input type="hidden" name="type" value="receipt">
            <input name="receipt_year"> 年
            <input name="receipt_month"> 月
            <input class="btn btn-primary"  type="submit" value="送出">
        </form>

        {{-- you should handle the empty array logic --}}
        @if (empty($objects))
            <h3>查無紀錄</h3>
        @else
            @component('layouts.tab')
                @slot('relation_titles')
                    @foreach($objects as $receiptKey => $object)
                        <li class="nav-item {{ $loop->first ? 'active' : ''  }}">
                            <a
                                class="nav-link {{ $loop->first ? 'active' : ''  }}"
                                data-toggle="tab"
                                href="#{{ $receiptKey }}-pane"
                                role="tab"
                            >
                                @lang("general.{$receiptKey}")
                            </a>
                        </li>
                    @endforeach
                @endslot
                @slot('relation_contents')
                    @foreach($objects as $receiptKey => $object)
                        @php
                            $active = $loop->first ? 'show active' : 'fade';
                        @endphp
                        <div class="tab-pane container {{ $active }}" id="{{$receiptKey}}-pane">
                            <form data-target="#receipts-table-{{$receiptKey}}" data-toggle="datatable-query">
                                <div class="query-box">
                                </div>
                                <i class="fa fa-plus-circle" data-toggle="datatable-query-add"></i>
                                <input type="submit" class="btn btn-sm btn-primary" value="搜尋">
                            </form>

                            <table id="receipts-table-{{$receiptKey}}" class="display table" style="width:100%">
                                <thead>
                                    @foreach(config('enums.'.$receiptKey) as $receiptColumn)
                                        <th>{{$receiptColumn}}</th>
                                    @endforeach
                                </thead>
                                <tbody>
                                    {{-- all the records --}}
                                    @foreach ( $object as $row )
                                        <tr>
                                            @foreach(config('enums.'.$receiptKey.'_en') as $receiptColumnEn)
                                                <td>@include('shared.helpers.value_helper', ['value' => $row[$receiptColumnEn]])</td>
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
    @foreach($objects as $receiptKey => $object)
        renderDataTable(["#receipts-table-{{$receiptKey}}"], 
            { "order": [],
               "drawCallback": function( settings ) {
                    $(".dataTables_wrapper").addClass('table-responsive');
                }
            }
        );
    @endforeach
</script>
