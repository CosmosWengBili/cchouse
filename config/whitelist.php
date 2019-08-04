<?php

return [
    'users' => ['id', 'name', 'email', 'mobile'],
    'audits' => [
        'id', 'user_type', 'user_id', 'event', 'auditable_type', 'auditable_id', 'old_values', 'new_values', 'url',
        'ip_address', 'user_agent', 'tags', 'created_at',
    ],
];
