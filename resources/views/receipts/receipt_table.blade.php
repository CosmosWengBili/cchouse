<div class="card">
    <div class="card-body table-responsive">
        <h2>
            收據報表
        </h2>
        <form action="/receipts" meth="GET">
            <input type="hidden" name="type" value="receipt">
            開始日期 <input type="date" name="start_date">
            結束日期 <input type="date" name="end_date">
            <input class="btn btn-primary"  type="submit" value="送出">
        </form>

        {{-- you should handle the empty array logic --}}
        @if (empty($receiptData))
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
                    <th>物件代碼</th>
                    <th>組別</th>
                    <th>縣市</th>
                    <th>區</th>
                    <th>地址</th>
                    <th>稅籍編號</th>
                    <th>大房東</th>
                    <th>應該給大房東租金</th>
                    <th>可認列租金支出</th>
                    <th>每月付款日</th>
                    <th>支出年</th>
                    <th>租金票到期日</th>
                </thead>
                <tbody>
                    {{-- all the records --}}
                    @foreach ( $objects as $object )
                        <tr>
                            <td>{{ $object['room_code']}}</td>
                            <td>{{ $object['group']}}</td>
                            <td>{{ $object['city']}}</td>
                            <td>{{ $object['district']}}</td>
                            <td>{{ $object['address']}}</td>
                            <td>{{ $object['tax_number']}}</td>
                            <td>{{ $object['landlord_name']}}</td>
                            <td>{{ $object['taxable_charter_fee']}}</td>
                            <td>{{ $object['actual_charter_fee']}}</td>
                            <td>{{ $object['rent_collection_time']}}</td>
                            <td>{{ $object['rent_collection_year']}}</td>
                            <td>{{ $object['commission_end_date']}}</td>
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
    