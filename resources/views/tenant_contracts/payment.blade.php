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
            @if(Route::current()->getName() != 'tenantContracts.extend')
            {{-- <tr>
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
            </tr> --}}
            @endif
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
                '        <select class="form-control form-control-sm subject" name="payments[' + idx + '][subject]"  value="">' +
                @foreach(config('enums.tenant_payments.subject') as $value)
                '            <option value="{{$value}}">{{$value}}</option>'+
                @endforeach
                '        </select>'+
                '    </td>' +
                '    <td>' +
                '        <select class="form-control form-control-sm period" name="payments[' + idx + '][period]"  value="">' +
                @foreach(config('enums.tenant_payments.period') as $value)
                '            <option value="{{$value}}">{{$value}}</option>'+
                @endforeach
                '        </select>'+
                '    </td>' +
                '    <td>' +
                '        <input type="text" class="form-control form-control-sm amount" name="payments[' + idx + '][amount]" value="">' +
                '    </td>' +
                '    <td>' +
                '        <select class="form-control form-control-sm collected_by" name="payments[' + idx + '][collected_by]"  value="">' +
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


        // For `tenantContracts.extend` route to generate default payments
        const appendOldPayments = () => {
            const tenantPayments = JSON.parse('{!! json_encode( isset($data["tenant_payment"]) ? $data["tenant_payment"] : null)!!}')
            tenantPayments.map(function (item, index) {
                const idx = $table.find('tbody > tr').length;
                const $template = $(buildTemplate(idx));

                $template.find(`select.subject option[value*="${item.subject}"]`).attr('selected', 'selected')
                $template.find(`select.period option[value*="${item.period}"]`).attr('selected', 'selected')
                $template.find(`select.collected_by option[value*="${item.collected_by}"]`).attr('selected', 'selected')
                $template.find("input.amount").val(item.amount)
                $template.insertBefore($table.find("tbody > tr:nth-child("+ (idx) +")"));
            });
        }
        appendOldPayments();



    })();
</script>
