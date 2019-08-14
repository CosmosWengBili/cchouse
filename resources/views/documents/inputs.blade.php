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
        <button class="btn btn-success js-add-row" type="button">新增</button>
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
        const $lastRow = $fileInputs.find('div:last-child');
        let idx = $fileInputs.children().length;
        const documentType = "{{ $documentType }}";
        const $addRow = $fileInputs.find('.js-add-row');
        $addRow.on('click', function () {
            const template = `
                <div class="form-group position-relative">
                    <div class="custom-file w-75">
                        <input
                            type="file"
                            name="documents[${documentType}][${idx}]"
                            class="custom-file-input"
                            id="documents-${documentType}-${idx}" />
                        <label class="custom-file-label" for="documents-${documentType}-${idx}" data-browse="瀏覽">選擇檔案</label>
                    </div>
                    <button class="mt-2 btn btn-danger btn-xs js-delete-btn" type="button">
                        <span class="fa fa-times"></span>
                    </button>
                </div>`;
            $(template).insertBefore($lastRow);
            idx += 1;
        });
    })();
</script>
