@extends('layouts.app')
@section('content')
<div class="container">
    <div class="justify-content-center">
        <div class="row my-3">
            <div class="col-12 card">
                <div class="card-body">
                    <div class="card-title">
                        詳細資料
                    </div>
                    <button class="btn btn-danger btn-lg" 
                            data-toggle="modal" 
                            data-target='#send-electricity-payment-report-sms-model'
                            data-tenant-contract-id="{{$data['id']}}">發送電費報表</button>
                    {{-- for showing the target returned --}}
                    <table class="table table-bordered">
                        @foreach ( $data as $attribute => $value)
                            @continue(is_array($value))
                            <tr>
                                <td>@lang("model.{$model_name}.{$attribute}")</td>
                                <td>
                                    @if(is_bool($value))
                                        {{ $value ? '是' : '否' }}
                                    @else
                                        {{ $value }}
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </table>
                </div>
            </div>

            @if (!empty($relations))
                {{-- you could propbly have many kinds of nested resources --}}
                @foreach($relations as $relation)
                    <div class="col-6 my-3">
                        {{-- handle first level of the nested resource, leave the others to recursion --}}
                        @php
                            $layer = Str::snake(explode('.', $relation)[0]);
                        @endphp
                        @if ( $layer == 'documents')
                            @include('documents.table', ['objects' => $data[$layer], 'layer' => $layer])
                        @elseif ( in_array( $layer , ['tenant', 'room', 'building']) )
                            @include('tenant_contracts.single_table', ['object' => $data[$layer], 'layer' => $layer])
                        @elseif ( $layer == 'payLogs' )
                            @include($layer . '.table', ['objects' => Arr::collapse(Arr::pluck($data['tenant_payments'], 'pay_logs')), 'layer' => $layer."s"])
                        @else
                            @include($layer . '.table', ['objects' => $data[$layer], 'layer' => $layer])
                        @endif
                    </div>
                @endforeach
            @endif
        </div>
    </div>
</div>
<div class="modal fade" tabindex="-1" role="dialog" id="send-electricity-payment-report-sms-model">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">發送電費報表簡訊 - 編號: <span class="js-fill-id"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="form-group row">
                        <label class="col-2 col-form-label" for="sms-year">年份：</label>
                        <div class="col-10">
                            <select name="year" id="sms-year" class="form-control form-control-sm">
                                @php
                                    $currentYear = \Carbon\Carbon::now()->year;
                                @endphp
                                @for ($i = 0; $i < 10; $i++)
                                    <option value="{{ $currentYear - $i }}">{{ $currentYear - $i }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-2 col-form-label" for="sms-month">年份：</label>
                        <div class="col-10">
                            <select name="month" id="sms-month" class="form-control">
                                @for ($i = 1; $i <= 12; $i++)
                                    <option value="{{ $i }}">{{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary js-submit">送出</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">關閉</button>
            </div>
        </div>
    </div>
</div>
<script>
(function() {
    let tenantContractId = null;
    $('#send-electricity-payment-report-sms-model').on('show.bs.modal', function (event) {
        const button = $(event.relatedTarget);
        tenantContractId = button.data('tenant-contract-id');

        $('#send-electricity-payment-report-sms-model span.js-fill-id').text(tenantContractId);
    })

    $('#send-electricity-payment-report-sms-model button.js-submit').on('click', function () {
        const year = $('select[name="year"]').val();
        const month = $('select[name="month"]').val();
        const data =  { year: year, month: month, tenantContractId: tenantContractId };

        $.post("{{ route('tenantContracts.sendElectricityPaymentReportSMS') }}", data, function () {
            alert('發送成功');
            $('#send-electricity-payment-report-sms-model').modal('hide');
            $('.modal-backdrop').remove();
        }).fail(function () {
            alert('發送失敗');
        })
    })
})();
</script>
@endsection
