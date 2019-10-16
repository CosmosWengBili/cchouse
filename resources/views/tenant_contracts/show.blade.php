@extends('layouts.app')
@section('content')
<div class="container">
    <div class="justify-content-center">
        <div class="row my-3">
            <div class="col-12 card">
                <div class="card-body">
                    @if($paid_diff != 0)
                        <div class="alert alert-danger" role="alert">
                            此案件繳費明細總額與銀行紀錄總額有差額： {{ $paid_diff }} 元。
                        </div>
                    @endif
                    <div class="card-title">
                        詳細資料
                        <a class="btn btn-primary" href="{{ route( 'tenantContracts.edit', $data['id']) }}">編輯</a>
                    </div>
                    <button class="btn btn-danger btn-lg"
                            data-toggle="modal"
                            data-target='#send-electricity-payment-report-sms-model'
                            data-tenant-contract-id="{{$data['id']}}">發送電費報表</button>
                    {{-- for showing the target returned --}}
                    <div class="row">
                        @foreach ( $data as $attribute => $value)
                            @continue(is_array($value))
                            <div class="col-3 border py-2 font-weight-bold">@lang("model.{$model_name}.{$attribute}")</div>
                            <div class="col-3 border py-2">
                                @include('shared.helpers.value_helper', ['value' => $value])
                            </div>
                        @endforeach
                    </div>
                </div>
                <hr>

                @component('layouts.tab')
                    {{-- other title of relation pages --}}
                    @slot('relation_titles')
                        @if (!empty($relations))
                            @foreach($relations as $key => $relation)
                                @php
                                    $layer = getLayer($relation);
                                    $title = __("model.{$model_name}.{$layer}");

                                    $active = $loop->first ? 'active' : '';
                                @endphp
                                <li class="nav-item">
                                    <a class="nav-link {{ $active }}" data-toggle="tab" href="#content-{{$key}}">{{$title}}</a>
                                </li>
                            @endforeach
                        @endif
                    @endslot

                    {{-- other contents of relation pages --}}
                    @slot('relation_contents')
                        {{-- display the next level nested resources --}}
                        @if (!empty($relations))
                            {{-- you could propbly have many kinds of nested resources --}}
                            @foreach($relations as $key => $relation)
                                @php
                                    $active = $loop->first ? 'active' : 'fade';
                                @endphp
                                <div class="tab-pane container {{ $active }}" id="content-{{$key}}">
                                    @php
                                        $layer = Str::snake(explode('.', $relation)[0]);
                                    @endphp

                                    @if ( $layer == 'documents')
                                        @include('documents.table', ['objects' => $data[$layer], 'layer' => $layer])
                                    @elseif ( in_array( $layer , ['tenant', 'room', 'building']) )
                                        @include('tenant_contracts.single_table', ['object' => $data[$layer], 'layer' => $layer])
                                    @elseif ( $layer == 'payLogs' )
                                        @include($layer . '.table', ['objects' => Arr::collapse(Arr::pluck($data['tenant_payments'], 'pay_logs')), 'layer' => $layer."s"])
                                    @elseif ( $layer == 'pay_off' )
                                        @include('pay_offs.table', ['objects' => [$data['pay_off']], 'layer' => 'pay_offs'])
                                    @else
                                        @include($layer . '.table', ['objects' => $data[$layer], 'layer' => $layer])
                                    @endif
                                </div>
                            @endforeach
                        @endif
                    @endslot
                @endcomponent
            </div>
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
