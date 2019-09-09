<div id="import-{{ $model }}" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">匯入 {{$model}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="{{$model}}-import-form" action="/import/{{$model}}" method="post" enctype="multipart/form-data">
                    @csrf
                    <input type="file" name="excel">
                    <input type="submit" class="btn btn-primary btn-block my-3" value="匯入">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">關閉</button>
                <a href="/example/{{$model}}" class="btn btn-secondary">下載範例檔案</a>
            </div>
        </div>
    </div>
</div>
