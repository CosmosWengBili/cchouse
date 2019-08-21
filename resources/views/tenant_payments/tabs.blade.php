<ul class="nav nav-tabs">
    <li class="nav-item">
        <a
            class="nav-link {{ $by == 'contract' ? 'active' : '' }}"
            href="{{ route('tenantPayments.index', ['by' => 'contract']) }}"
        >
            By Contract
        </a>
    </li>
    <li class="nav-item">
        <a
            class="nav-link {{ $by == 'date' ? 'active' : '' }}"
            href="{{ route('tenantPayments.index', ['by' => 'date']) }}"
        >
            By Date
        </a>
    </li>
</ul>
