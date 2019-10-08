<div id="import-tenant_electricity_payments" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">匯入電費</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="tenant_electricity_payments-import-form" action="{{ route('tenantElectricityPayments.importFile') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <input type="file" name="excel">
                    <input type="submit" class="btn btn-primary btn-block my-3" value="匯入">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">關閉</button>
                <a href="{{ route('tenantElectricityPayments.downloadImportFile')}}" class="btn btn-secondary">下載範例檔案</a>
            </div>
        </div>
    </div>
</div>
