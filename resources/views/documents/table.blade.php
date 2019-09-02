<div class="card">
    <div class="card-body table-responsive">
        <h2>
            相關文件
        </h2>

        {{-- you should handle the empty array logic --}}
        @if (empty($objects))
            <h3>尚無紀錄</h3>
        @else
            <table class="display table" style="width:100%">
                <thead>
                    <th>檔名</th>
                    <th>檔案類別</th>
                    <th>建立時間</th>
                </thead>
                <tbody>
                    {{-- all the records --}}
                    @foreach ( $objects as $object )
                        <tr>
                            <td>
                                <a
                                href="{{ Storage::url($object['path']) }}"
                                download="{{ $object['filename'] }}"
                                target="_blank"
                                >
                                {{ $object['filename'] }}
                                </a>
                            </td>
                            <td>{{ config('enums.documents.' . $object['document_type']) }}</td>
                            <td>{{ $object['created_at'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>
