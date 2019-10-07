@extends('layouts.app')
@section('content')
<input id="csrf" type="hidden" value={{ csrf_token() }}>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12 mt-4">
            {{-- for showing multiple types of entries returned --}}
            @foreach ( $data as $type => $entries)
                @include('shareholders.table', ['objects' => $entries, 'layer' => $type])
            @endforeach
        </div>
    </div>
</div>
@endsection

@section('bootstrap_modal')
    <div class="modal fade" id="modal">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">

                <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="modal-title">出帳明細</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <!-- Modal body -->
                <div class="modal-body">
                    <div>
                        <label for="year" class="mr-sm-2 w-25">年:</label>
                        <input type="number" maxlength="4" class="form-control form-control-sm w-50" id="year">
                    </div>
                    <div class="mt-1">
                        <label for="month" class="mr-sm-2 w-25">月:</label>
                        <input type="number" min="1" max="12" class="form-control form-control-sm w-50" id="month">
                    </div>
                </div>

                <!-- Modal footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-primary send">匯出</button>
                </div>

            </div>
        </div>
    </div>

    <script>
        $('#modal button.send').click(function () {
            const year = $('#year').val();
            const month = $('#month').val();
            const export_url = `{{ route('shareholders.export') }}?year=${year}&month=${month}`;
            $('<a>').attr('href', export_url)[0].click()
        })
    </script>
@endsection


