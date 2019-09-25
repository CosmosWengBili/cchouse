<table id="{{$prefix}}-table" class="table table-striped">
    <thead>
    <tr>
        <td>類別</td>
        <td>資料</td>
        <td></td>
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
                <div>
                    <button class="btn btn-danger btn-xs js-remove-contact-info-row" type="button">X</button>
                </div>
            </td>
        </tr>
    @endforeach
    <tr>
        <td colspan="3" class="text-center">
            <button class="btn btn-success js-add-contact-info-row" type="button">新增</button>
        </td>
    </tr>
    </tbody>
</table>


<script>
    (function () {
        var buildTemplate = function (idx, form_index) {
            return `
            <tr>
                <td>
                    <select
                        class="form-control form-control-sm"
                        name="{{$prefix}}[${form_index}][${idx}][info_type]"
                    >
                        <option value="phone">聯絡電話</option>
                        <option value="mailing_address">聯絡地址</option>
                        <option value="email">電子郵件</option>
                        <option value="fax_number">傳真</option>
                    </select>
                </td>
                <td>
                    <input
                        type="text"
                        class="form-control form-control-sm"
                        name="{{$prefix}}[${form_index}][${idx}][value]"
                    />
                </td>
                <td>
                    <div>
                        <button class="btn btn-danger btn-xs js-remove-contact-info-row" type="button">X</button>
                    </div>
                </td>
            </tr>
            `
        };

        // must to unbind and then rebind
        $(document).off('click', 'button.js-add-contact-info-row');
        $(document).on('click', 'button.js-add-contact-info-row', function () {
            const form_index = $(this).parents('div.duplicate').attr('data-form-index');
            var idx = $(this).parents('tbody').find('tr').length;
            var $insertRow = $(this).parents('tbody').find('tr:last-child');
            $(buildTemplate(idx, form_index)).insertBefore($insertRow);
        });

        // must to unbind and then rebind
        $(document).off('click', 'button.js-remove-contact-info-row');
        $(document).on('click', 'button.js-remove-contact-info-row', function () {
            $(this).closest('tr').remove();
        });
    })();
</script>
