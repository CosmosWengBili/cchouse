
<div>
<table id="maintenance-table" class="table table-striped">
        <tbody>
            @foreach($maintenances as $idx => $maintenance)
            <tr class="maintenance-row">
                <input type="hidden" name="{{ "maintenances[{$idx}][id]" }}" value="{{ $maintenance['id'] }}">
                <td style="width:10%">清潔人員/廠商</td>
                <td style="width:15%">
                    <input
                        class="form-control form-control-sm"
                        type="text"
                        name="{{ "maintenances[{$idx}][maintainer]" }}"
                        value="{{ $maintenance['maintainer'] }}"
                    />
                </td>
                <td style="width:10%">清潔位置</td>
                <td style="width:15%">
                    <input
                        class="form-control form-control-sm"
                        type="text"
                        name="{{ "maintenances[{$idx}][maintained_location]" }}"
                        value="{{ $maintenance['maintained_location'] }}"
                    />
                </td>
                <td style="width:5%">清潔日期</td>
                <td style="width:15%">
                    <input
                        class="form-control form-control-sm"
                        type="date"
                        name="{{ "maintenances[{$idx}][maintained_date]" }}"
                        value="{{ $maintenance['maintained_date'] }}"
                    />
                </td>
                <td style="width:10%">
                    <button class="btn btn-danger btn-xs js-remove-row" type="button">X</button>
                </td>
            </tr>
            @endforeach
            <tr>
                <td colspan="7" class="text-center">
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
                '<tr class="maintenance-row">'+
                '<td style="width:10%">清潔人員/廠商</td>'+
                '<td style="width:15%">'+
                '    <input' +
                '        class="form-control form-control-sm is-valid"' +
                '        type="text" name="maintenances[' + idx + '][maintainer]"' +
                '        value="" '+
                '        aria-invalid="false">'+
                '</td>'+
                '<td style="width:10%">清潔位置</td>'+
                '<td style="width:15%">'+
                '    <input' +
                '        class="form-control form-control-sm"' +
                '        type="text" name="maintenances[' + idx + '][maintained_location]"' +
                '        value="">'+
                '</td>'+
                '<td style="width:5%">清潔日期</td>'+
                '<td style="width:15%">'+
                '    <input' +
                '        class="form-control form-control-sm" '+
                '        type="date"' +
                '        name="maintenances[' + idx + '][maintained_date]"' +
                '        value="">'+
                '</td>'+
                '<td style="width:10%">'+
                '    <button' +
                '    class="btn btn-danger btn-xs js-remove-row"' +
                '    type="button">X'+
                '    </button>'+
                '</td>'+
                '</tr>'
            );
        }
        var $table = $("#maintenance-table");
        var $insertRow = $table.find('tbody > tr:last-child');

        // 這邊會有問題, 在刪除中間又新增時
        $table.find('.js-add-row').on('click', function () {
            var idx = $table.find('tbody > tr.maintenance-row').length + 1;
            $(buildTemplate(idx)).insertBefore($insertRow);
            window.realtimeSelect($('[data-toggle=selectize]'))
        });
        $table.on('click', '.js-remove-row', function () {
            $middle = $(this).closest('tr')
            $middle.remove();
        });
    })();
</script>