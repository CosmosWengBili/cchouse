<?php
return [
    'users' => ['id', 'name', 'email', 'mobile'],
    'audits' => [
        'id', 'user_type', 'user_id', 'event', 'auditable_type', 'auditable_id', 'old_values', 'new_values', 'url',
        'ip_address', 'user_agent', 'tags', 'created_at',
    ],
    'tenants' => [
        'id', 'name', 'certificate_number', 'is_legal_person', 'line_id', 'residence_address', 'company',
        'job_position', 'company_address', 'confirm_by', 'confirm_at',
    ],
    'landlords' =>[
        'id', 'name', 'certificate_number'
    ],
    'landlord_contracts' => [
        'id','commission_start_date'
    ],
    'debt_collections' => [
      'id', 'collector_id', 'tenant_contract_id', 'details', 'is_penalty_collected', 'comment', 'created_at'
    ],
    'buildings' => [],
    'tenant_contracts' => [],
    'landlord_payments' => [],
    'keys' => [],
    'share_holders' => []
];
