<table id="{{$prefix}}-table" class="table table-striped">
    <thead>
    <tr>
        <td>姓名</td>
        <td>證號</td>
        <td>聯絡電話</td>
        <td>戶籍地址</td>
        <td>聯絡地址</td>
        <td>電子郵件</td>
    </tr>
    </thead>
    <tbody>
    @foreach($agents as $idx => $agent)
        <tr>
            <td>
                <input type="hidden" name="{{ "{$prefix}[{$idx}][id]" }}" value="{{ $agent['id'] }}" />
                <input
                    type="text"
                    class="form-control form-control-sm"
                    name="{{ "{$prefix}[{$idx}][name]" }}"
                    value="{{ $agent['name'] }}"
                />
            </td>
            <td>
                <input
                    type="text"
                    class="form-control form-control-sm"
                    name="{{ "{$prefix}[{$idx}][certificate_number]" }}"
                    value="{{ $agent['certificate_number'] }}"
                />
            </td>
            <td>
            <input
                    type="text"
                    class="form-control form-control-sm"
                    name="{{ "{$prefix}[{$idx}][phone]" }}"
                    value="{{ $agent['phone'] }}"
                />
            </td>
            <td>
                <input
                    type="text"
                    class="form-control form-control-sm"
                    name="{{ "{$prefix}[{$idx}][residence_address]" }}"
                    value="{{ $agent['residence_address'] }}"
                />
            </td>
            <td>
                <input
                    type="text"
                    class="form-control form-control-sm"
                    name="{{ "{$prefix}[{$idx}][mailing_address]" }}"
                    value="{{ $agent['mailing_address'] }}"
                />
            </td>
            <td>
                <input
                    type="text"
                    class="form-control form-control-sm"
                    name="{{ "{$prefix}[{$idx}][email]" }}"
                    value="{{ $agent['email'] }}"
                />
            </td>
            <td>
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


<script>
    (function () {
        var buildTemplate = function (idx) {
            return (
                '<tr>' +
                '    <td>' +
                '        <input type="text" class="form-control form-control-sm" name="{{$prefix}}[' + idx + '][name]" value="">' +
                '    </td>' +
                '    <td>' +
                '        <input type="text" class="form-control form-control-sm" name="{{$prefix}}[' + idx + '][certificate_number]" value="">' +
                '    </td>' +
                '    <td>' +
                '        <input type="text" class="form-control form-control-sm" name="{{$prefix}}[' + idx + '][phone]" value="">' +
                '    </td>' +
                '    <td>' +
                '        <input type="text" class="form-control form-control-sm" name="{{$prefix}}[' + idx + '][residence_address]" value="">' +
                '    </td>' +
                '    <td>' +
                '        <input type="text" class="form-control form-control-sm" name="{{$prefix}}[' + idx + '][mailing_address]" value="">' +
                '    </td>' +
                '    <td>' +
                '        <input type="text" class="form-control form-control-sm" name="{{$prefix}}[' + idx + '][email]" value="">' +
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
