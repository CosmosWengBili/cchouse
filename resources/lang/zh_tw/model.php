<?php
return [
    'User' => [
        'id' => '編號',
        'name' => '姓名',
        'email' => '電子郵件',
        'mobile' => '聯絡電話',
        'password' => '密碼'
    ],
    'Audit' => [
        'id' => '編號',
        'user_type' => 'User Model',
        'user_id' => 'User ID',
        'event' => '事件',
        'auditable_type' => 'Model',
        'auditable_id' => 'Model ID',
        'old_values' => '變更前資料',
        'new_values' => '變更後資料',
        'url' => '操作網址',
        'ip_address' => 'IP Address',
        'user_agent' => 'User Agent',
        'tags' => '標籤',
        'created_at' => '發生時間',
    ]
];
?>
