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
        'id', 'commission_start_date', 'building_id'
    ],
    'debt_collections' => [
      'id', 'collector_id', 'tenant_contract_id', 'details', 'is_penalty_collected', 'comment', 'created_at'
    ],
    'buildings' => [],
    'tenant_contracts' => ['id', 'contract_start', 'contract_end'],
    'landlord_payments' => [],
    'keys' => [],
    'share_holders' => [],
    'deposits' => [],
    'maintenances' => [
        'id', 'reported_at', 'expected_service_date', 'expected_service_time', 'dispatch_date',
        'commissioner_id', 'maintenance_staff_id', 'closed_date', 'closed_comment', 'service_comment', 'status',
        'incident_details', 'incident_type', 'work_type', 'number_of_times', 'payment_request_date',
        'closing_serial_number', 'billing_details', 'payment_request_serial_number', 'cost', 'price', 'afford_by',
        'is_recorded', 'comment', 'created_at', 'updated_at', 'is_printed'
    ]
];
