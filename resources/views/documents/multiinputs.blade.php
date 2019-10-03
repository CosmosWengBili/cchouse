@php
$documents = isset($documents) ? $documents : [];
$fileInputsClassName = 'file-inputs-' . rand(0,99999);
@endphp

<div class="{{ $fileInputsClassName }}">
    @foreach($documents as $idx => $document)
        <div class="form-group position-relative">
            <div class="d-inline-block w-75">
                檔案：
                <a
                    href="{{ $document->url() }}"
                    download="{{ $document->filename }}"
                    target="_blank"
                >
                    {{ $document->filename }}
                </a>
                <input type="hidden" name="documents[{{ $documentType }}][{{$idx}}][id]" value="{{$document->id}}" />
                <input type="hidden" name="documents[{{ $documentType }}][{{$idx}}][_delete]" value="0" />
            </div>
            <button class="mt-2 btn btn-danger btn-xs js-delete-btn" type="button">
                <span class="fa fa-times"></span>
            </button>
        </div>
    @endforeach

    <div class="text-center">
        <button class="btn btn-success js-add-row {{ $documentType }}" data-document-type="{{ $documentType }}" type="button">新增</button>
    </div>
</div>

<script>
    (function () {
        const $fileInputs = $('.{{ $fileInputsClassName }}');

        // 顯示檔案名稱
        $fileInputs.on('change', '.custom-file-input', function(){
            const fileName = $(this).val().replace('C:\\fakepath\\', " ");
            //replace the "Choose a file" label
            $(this).next('.custom-file-label').html(fileName);
        });

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
        {{--const documentType = "{{ $documentType }}";--}}

        // must to unbind and then rebind
        $(document).off('click', 'button.js-add-row.{{ $documentType }}')
        $(document).on('click', 'button.js-add-row.{{ $documentType }}', function () {
            const idx = $(this).parent().parent().children().length - 1
            const form_index = $(this).parents('div.duplicate').attr('data-form-index');
            const $last_row = $(this).parent().parent().find('div:last-child');
            let id;
            if (typeof form_index === 'string') {
                id = `documents[{{ $documentType }}][${form_index}][${idx}]`;
            } else {
                id = `documents[{{ $documentType }}][${idx}]`;
            }

            const template = `
                <div class="form-group position-relative">
                    <div class="custom-file w-75">
                        <input
                            type="file"
                            name="${id}"
                            class="custom-file-input"
                            id="${id}" />
                        <label class="custom-file-label overflow-hidden" for="${id}" data-browse="瀏覽">選擇檔案</label>
                    </div>
                    <button class="mt-2 btn btn-danger btn-xs js-delete-btn" type="button">
                        <span class="fa fa-times"></span>
                    </button>
                </div>`;
            $(template).insertBefore($last_row);
        });
    })();
</script>
