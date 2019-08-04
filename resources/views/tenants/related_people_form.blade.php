<table id="{{$prefix}}-table" class="table table-striped">
    <thead>
    <tr>
        <td>姓名</td>
        <td>電話</td>
        <td>關係</td>
        <td>功能</td>
    </tr>
    </thead>
    <tbody>
    @foreach($relatedPeople as $idx => $relatedPerson)
        <tr>
            <td>
                <input type="hidden" name="{{ "{$prefix}[{$idx}][id]" }}" value="{{ $relatedPerson['id'] }}" />
                <input
                    type="text"
                    class="form-control form-control-sm"
                    name="{{ "{$prefix}[{$idx}][name]" }}"
                    value="{{ $relatedPerson['name'] }}"
                />
            </td>
            <td>
                <input
                    type="text"
                    class="form-control form-control-sm"
                    name="{{ "{$prefix}[{$idx}][phone]" }}"
                    value="{{ $relatedPerson['phone'] }}"
                />
            </td>
            <td>
                <input
                    type="text"
                    class="form-control form-control-sm"
                    name="{{ "{$prefix}[{$idx}][relationship]" }}"
                    value="{{ $relatedPerson['relationship'] }}"
                />
            </td>
            <td>
                <button class="btn btn-danger btn-xs js-remove-row" type="button">X</button>
            </td>
        </tr>
    @endforeach
    <tr>
        <td colspan="4" class="text-center">
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
                '        <input type="text" class="form-control form-control-sm" name="{{$prefix}}[' + idx + '][phone]" value="">' +
                '    </td>' +
                '    <td>' +
                '        <input type="text" class="form-control form-control-sm" name="{{$prefix}}[' + idx + '][relationship]" value="">' +
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
