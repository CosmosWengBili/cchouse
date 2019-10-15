<ul class="nav nav-tabs">
    <li class="nav-item">
        <a
            class="nav-link {{ $by == 'contract' ? 'active' : '' }}"
            href="{{ route('payLogs.index', ['by' => 'contract']) }}"
        >
            根據合約
        </a>
    </li>
    <li class="nav-item">
        <a
            class="nav-link {{ $by == 'date' ? 'active' : '' }}"
            href="{{ route('payLogs.index', ['by' => 'date']) }}"
        >
            根據日期
        </a>
    </li>
</ul>
