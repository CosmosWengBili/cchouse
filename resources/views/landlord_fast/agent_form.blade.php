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
                <button class="btn btn-danger btn-xs js-remove-agent-form-row" type="button">X</button>
            </td>
        </tr>
    @endforeach
    <tr>
        <td colspan="7" class="text-center">
            <button class="btn btn-success js-add-agent-form-row" type="button">新增</button>
        </td>
    </tr>
    </tbody>
</table>


<script>
    (function () {
        var buildTemplate = function (idx, form_index) {
            return (
                '<tr>' +
                '    <td>' +
                '        <input type="text" class="form-control form-control-sm" name="{{$prefix}}[' + form_index + '][' + idx + '][name]">' +
                '    </td>' +
                '    <td>' +
                '        <input type="text" class="form-control form-control-sm" name="{{$prefix}}[' + form_index + '][' + idx + '][certificate_number]">' +
                '    </td>' +
                '    <td>' +
                '        <input type="text" class="form-control form-control-sm" name="{{$prefix}}[' + form_index + '][' + idx + '][phone]">' +
                '    </td>' +
                '    <td>' +
                '        <input type="text" class="form-control form-control-sm" name="{{$prefix}}[' + form_index + '][' + idx + '][residence_address]">' +
                '    </td>' +
                '    <td>' +
                '        <input type="text" class="form-control form-control-sm" name="{{$prefix}}[' + form_index + '][' + idx + '][mailing_address]">' +
                '    </td>' +
                '    <td>' +
                '        <input type="text" class="form-control form-control-sm" name="{{$prefix}}[' + form_index + '][' + idx + '][email]">' +
                '    </td>' +
                '    <td>' +
                '        <button class="btn btn-danger btn-xs js-remove-agent-form-row" type="button">X</button>' +
                '    </td>' +
                '</tr>'
            );
        }

        // must to unbind and then rebind
        $(document).off('click', 'button.js-add-agent-form-row');
        $(document).on('click', 'button.js-add-agent-form-row', function () {
            const form_index = $(this).parents('div.duplicate').attr('data-form-index');
            var idx = $(this).parents('tbody').find('tr').length;
            var $insertRow = $(this).parents('tbody').find('tr:last-child');
            $(buildTemplate(idx, form_index)).insertBefore($insertRow);
        });

        // must to unbind and then rebind
        $(document).off('click', 'button.js-remove-agent-form-row');
        $(document).on('click', 'button.js-remove-agent-form-row', function () {
            $(this).closest('tr').remove();
        });
    })();
</script>
