<div class="card">
    <div class="card-body table-responsive">
        <h2>
            收據報表
            <a class="btn btn-sm btn-success" href="export/function/{{$type}}/?start_date={{$start_date}}&end_date={{$end_date}}">輸出報表</a>
        </h2>
        <form action="/receipts" meth="GET">
            <input type="hidden" name="type" value="receipt">
            開始日期 <input type="date" name="start_date">
            結束日期 <input type="date" name="end_date">
            <input class="btn btn-primary"  type="submit" value="送出">
        </form>

        {{-- you should handle the empty array logic --}}
        @if (empty($objects))
            <h3>查無紀錄</h3>
        @else
            <form data-target="#receipts-table" data-toggle="datatable-query">
                <div class="query-box">
                </div>
                <i class="fa fa-plus-circle" data-toggle="datatable-query-add"></i>
                <input type="submit" class="btn btn-sm btn-primary" value="搜尋">
            </form>

            <table id="receipts-table" class="display table" style="width:100%">
                <thead>
                    @foreach(config('enums.receipt') as $receipt_column)
                        <th>{{$receipt_column}}</th>
                    @endforeach
                </thead>
                <tbody>
                    {{-- all the records --}}
                    @foreach ( $objects as $object )
                        <tr>
                            @foreach(config('enums.receipt_en') as $receipt_key)
                                <td>{{ $object[$receipt_key]}}</td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>
<script>
    renderDataTable(["#receipts-table"]);
</script>
    