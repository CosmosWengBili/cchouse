@php
$fileInputsClassName = 'file-inputs-' . rand(0,99999);
@endphp
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
        <tr class="maintenance-picture-row">
            <td style="width:5%">照片</td>
            <td style="width:15%" colspan="6">
                <div class="{{$fileInputsClassName}}">
                    @if (count($maintenance['pictures']) > 0 && $maintenance['pictures'])
                        @foreach($maintenance['pictures'] as $documentIdx => $document)
                        <div class="form-group position-relative">
                            <div class="d-inline-block w-75">
                                檔案：
                                <a
                                    href="{{ $document['url'] }}"
                                    download="{{ $document['filename'] }}"
                                    target="_blank"
                                >
                                    {{ $document['filename'] }}
                                </a>
                                <input
                                    type="hidden"
                                    name="{{ "maintenances[{$idx}][pictures][{$documentIdx}][id]" }}"
                                    value="{{$document['id']}}"
                                />
                                <input
                                    type="hidden"
                                    name="{{ "maintenances[{$idx}][pictures][{$documentIdx}][_delete]" }}"
                                    value="0"
                                />
                            </div>
                            <button class="mt-2 btn btn-danger btn-xs js-delete-btn" type="button">
                                <span class="fa fa-times"></span>
                            </button>
                        </div>
                        @endforeach
                    @endif
                    <div class="text-center">
                        <button
                            class="btn btn-success js-add-file-row"
                            data-idx="{{$idx}}"
                            type="button">
                            新增
                        </button>
                    </div>
                </div>
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

        var bindChangFileName = function(){
            $('.custom-file-input').change(function(){
                const fileName = $(this).val().replace('C:\\fakepath\\', " ");
                //replace the "Choose a file" label
                $(this).next('.custom-file-label').html(fileName);
            })
        }


        bindChangFileName();

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
                '</tr>'+
                `
                <tr class="maintenance-picture-row">
                    <td style="width:5%">照片</td>
                    <td style="width:15%" colspan="6">
                        <div class="file-inputs-91409">

                    <div class="form-group position-relative">
                        <div class="custom-file w-75">
                            <input type="file" name="maintenances[${idx}][pictures][0][id]" class="custom-file-input">
                            <label class="custom-file-label" data-browse="瀏覽">
                                選擇檔案
                            </label>
                        </div>
                        <button class="mt-2 btn btn-danger btn-xs js-delete-btn" type="button">
                            <span class="fa fa-times"></span>
                        </button>
                    </div>
                    <div class="text-center">
                                <button class="btn btn-success js-add-file-row" data-idx="1" type="button">
                                    新增
                                </button>
                            </div>
                        </div>
                    </td>
                </tr>
                `
            );
        }
        var $table = $("#maintenance-table");
        var $insertRow = $table.find('tbody > tr:last-child');

        // 這邊會有問題, 在刪除中間又新增時
        $table.find('.js-add-row').on('click', function () {
            var idx = $table.find('tbody > tr.maintenance-row').length + 1;
            $(buildTemplate(idx)).insertBefore($insertRow);
            window.realtimeSelect($('[data-toggle=selectize]'))

            bindChangFileName();
        });
        $table.on('click', '.js-remove-row', function () {
            $middle = $(this).closest('tr')
            $middle_picture = $middle.next()
            $middle.remove();
            $middle_picture.remove();
        });



        const $fileInputs = $('.{{ $fileInputsClassName }}');

        // 顯示檔案名稱
        // $fileInputs.on('change', '.custom-file-input', function(){
        //     const fileName = $(this).val().replace('C:\\fakepath\\', " ");
        //     //replace the "Choose a file" label
        //     $(this).next('.custom-file-label').html(fileName);
        // });

        // 移除檔案
        $fileInputs.on('click', '.js-delete-btn', function () {
            const $deleteBtn = $(this);
            const $formGroup = $deleteBtn.parent();
            const $deleteInput = $formGroup.find('input[value="0"]');

            if ($deleteInput.length > 0) { // existed file
                $deleteInput.val(1); // 標記 `_delete` value 為 1
                $formGroup.slideUp();
            } else {
                $formGroup.slideUp(400, function () {
                    $formGroup.remove(); // 直接移除 form group
                });
            }
        });

        // 新增檔案
        $('.js-add-file-row').on('click', function () {
            $buttonDiv = $(this).parent()
            $idx = $(this).data('idx');
            $length = $buttonDiv.siblings('.form-group').length;

            const template = `
            <div class="form-group position-relative">
                <div class="custom-file w-75">
                    <input type="file" name="maintenances[${$idx}][pictures][${$length}][id]" class="custom-file-input">
                    <label class="custom-file-label" data-browse="瀏覽">
                        選擇檔案
                    </label>
                </div>
                <button class="mt-2 btn btn-danger btn-xs js-delete-btn" type="button">
                    <span class="fa fa-times"></span>
                </button>
            </div>
            `;

            $buttonDiv.before(template);
            bindChangFileName();
        });

    })();
</script>