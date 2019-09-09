<table id="{{$prefix}}-table" class="table table-striped">
    <thead>
    <tr>
        <td>類別</td>
        <td>資料</td>
    </tr>
    </thead>
    <tbody>
    @foreach($contact_infos as $idx => $contact_info)
        <tr>
            <td>
                <input type="hidden" name="{{ "{$prefix}[{$idx}][id]" }}" value="{{ $contact_info['id'] }}" />
                <select 
                    class="form-control form-control-sm"
                    name="{{ "{$prefix}[{$idx}][info_type]" }}"
                    value="{{ $contact_info['info_type'] }}"
                >
                    <option value="phone">聯絡電話</option>
                    {{--<option value="residence_address">戶籍地址</option>--}}
                    <option value="mailing_address">聯絡地址</option>
                    <option value="email">電子郵件</option>
                    <option value="fax_number">傳真</option>
                </select>
            </td>
            <td>
                <input
                    type="text"
                    class="form-control form-control-sm"
                    name="{{ "{$prefix}[{$idx}][value]" }}"
                    value="{{ $contact_info['value'] }}"
                />
            </td>
            <td>
                <button class="btn btn-danger btn-xs js-remove-row" type="button">X</button>
            </td>
        </tr>
    @endforeach
    <tr>
        <td colspan="3" class="text-center">
            <button class="btn btn-success js-add-row" type="button">新增</button>
        </td>
    </tr>
    </tbody>
</table>


<script>
    (function () {
        var buildTemplate = function (idx) {
            return (
                '<tr>' +
                '    <td>' +
                '        <select class="form-control form-control-sm" name="{{$prefix}}[' + idx + '][info_type]">' +
                '            <option value="phone">聯絡電話</option> ' +
                '            <option value="residence_address">戶籍地址</option> ' +
                '            <option value="mailing_address">聯絡地址</option> ' +
                '            <option value="email">電子郵件</option> ' +
                '            <option value="fax_number">傳真</option> ' +
                '        </select>' +
                '    </td>' +
                '    <td>' +
                '        <input type="text" class="form-control form-control-sm" name="{{$prefix}}[' + idx + '][value]" value="">' +
                '    </td>' +
                '    <td>' +
                '        <button class="btn btn-danger btn-xs js-remove-row" type="button">X</button>' +
                '    </td>' +
                '</tr>'
            );
        }
        var $table = $("#{{$prefix}}-table");
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
