
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
<link rel="stylesheet" href={{ asset('css/app.css') }}>

<div class="row">
    <div class="col">
        <div class="card">
            <div class="card-body">
                <h3>兆基物業管理 - 房客電費報表</h3>
                <div>
                    <span class="d-inline-block mr-3">年度：{{ $year }}</span>
                    <span class="d-inline-block mr-3" >月度：{{ $month }} </span>
                    <span class="d-inline-block mr-3">抄表日：{{ $ammeterReadDate ? $ammeterReadDate->format('Y/m/d') : '' }}</span>
                </div>
                @forelse($reportRows as $reportRow)
                    <div class="table-responsive my-3">
                        <table class="table table-bordered" style="table-layout: fixed;">
                            <thead>
                            <tr>
                                @foreach($reportRow as $header => $value)
                                    <th style="width: 110px;">{{$header}}</th>
                                @endforeach
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                @foreach($reportRow as $header => $value)
                                    <td>{{ $value }}</td>
                                @endforeach
                            </tr>
                            </tbody>
                        </table>
                    </div>
                @empty
                    <h4 class="text-center">查無應繳電費資料</h4>
                @endforelse

                @if(count($reportRows) > 0)
                    <hr />
                    <p class="help-block">
                        @php
                            $startYear = $month - 3 <= 0 ? $year - 1 : $year;
                            $startMonth = (($month + 12) - 3) % 12;
                        @endphp
                        <p class="h3">※ 依合約約定，遲繳 4 天以上將會產生行政處理費</p>
                    </p>
                @endif
            </div>
        </div>
    </div>
</div>
