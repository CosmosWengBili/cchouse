<div>
    {{-- generate the following block to have more payments --}}
    {{-- important: specify the id increment using js or whatever way you like in name attribute --}}
    {{-- payments[0][subject], payments[1][subject], payments[2][subject] ... --}}
    <table id="payments-table" class="table table-striped">
        <thead>
            <tr>
                <td>科目</td>
                <td>頻率</td>
                <td>費用</td>
                <td>收取</td>
                <td></td>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <select
                        class="form-control form-control-sm"
                        name="payments[0][subject]"
                        value=""
                    />
                        @foreach(config('enums.tenant_payments.subject') as $value)
                            <option value="{{$value}}">{{$value}}</option>
                        @endforeach
                    </select>                                            
                </td>
                <td>
                    <select
                        class="form-control form-control-sm"
                        name="payments[0][period]"
                        value=""
                    />
                        @foreach(config('enums.tenant_payments.period') as $value)
                            <option value="{{$value}}">{{$value}}</option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <input
                        class="form-control form-control-sm"
                        type="number"
                        name="payments[0][amount]"
                    />
                </td>
                <td>
                    <select
                        class="form-control form-control-sm"
                        name="payments[0][collected_by]"
                        value=""
                    />
                        @foreach(config('enums.tenant_payments.collected_by') as $value)
                            <option value="{{$value}}">{{$value}}</option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <button class="btn btn-danger btn-xs js-remove-row" type="button">X</button>
                </td>
            </tr>
            <tr>
                <td colspan="5" class="text-center">
                    <button class="btn btn-success js-add-row" type="button">新增</button>
                </td>
            </tr>
        </tbody>
    </table>
</div>
<script>
    (function () {
        var buildTemplate = function (idx) {
            return (
                '<tr>' +
                '    <td>' +
                '        <select class="form-control form-control-sm" name="payments[' + idx + '][subject]"  value="">' +
                @foreach(config('enums.tenant_payments.subject') as $value)
                '            <option value="{{$value}}">{{$value}}</option>'+
                @endforeach
                '        </select>'+
                '    </td>' +
                '    <td>' +
                '        <select class="form-control form-control-sm" name="payments[' + idx + '][period]"  value="">' +
                @foreach(config('enums.tenant_payments.period') as $value)
                '            <option value="{{$value}}">{{$value}}</option>'+
                @endforeach
                '        </select>'+
                '    </td>' +
                '    <td>' +
                '        <input type="text" class="form-control form-control-sm" name="payments[' + idx + '][amount]" value="">' +
                '    </td>' +
                '    <td>' +
                '        <select class="form-control form-control-sm" name="payments[' + idx + '][collected_by]"  value="">' +
                @foreach(config('enums.tenant_payments.collected_by') as $value)
                '            <option value="{{$value}}">{{$value}}</option>'+
                @endforeach
                '        </select>'+
                '    </td>' +
                '    <td>' +
                '        <button class="btn btn-danger btn-xs js-remove-row" type="button">X</button>' +
                '    </td>' +
                '</tr>'
            );
        }
        var $table = $("#payments-table");
        var $insertRow = $table.find('tbody > tr:last-child');

        $table.find('.js-add-row').on('click', function () {
            var idx = $table.find('tbody > tr').length;
            $(buildTemplate(idx)).insertBefore($insertRow);
        });
        $table.on('click', '.js-remove-row', function () {
            $(this).closest('tr').remove();
        });
    })();
</script>