<div id="maintenance-record-model" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">過去三個月的紀錄</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="overflow-x: auto">
                @if ($maintenances[0])
                <table id="record-table" class="display table" style="width:100%">
                    <thead>
                    @foreach ( array_keys($maintenances[0]) as $field)
                        <th>@lang("model.Maintenance.{$field}")</th>
                    @endforeach
                    </thead>
                    <tbody>
                    </tbody>
                </table>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">關閉</button>
            </div>
        </div>
    </div>
</div>
