<ul class="nav nav-tabs justify-content-center">
    @foreach( $month_options as $option )
    <li class="nav-item">
        <a
            class="nav-link 
            @if( $option['month'] == $report_used_date['month'] && $option['year'] == $report_used_date['year'] )
                bg-primary
            @endif
            "
            href="{{ route('monthlyReports.show', ['landlord_contract' => 1]) }}?month={{ $option['month'] }}&year={{ $option['year'] }}"
        >
            {{ $option['year'] }}/{{ $option['month'] }}
        </a>
    </li>
    @endforeach
</ul>
    