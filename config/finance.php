<?php

return [

    /*
    |--------------------------------------------------------------------------
    | All finance related stuff
    |--------------------------------------------------------------------------
    |
    |
    */

    'bank_code' => 952,

    // default options and order of reversal
    'reversal' => [
        '履約保證金',
        '更改繳款日租金',
        '仲介費',
        '清潔費(公司)',
        '顧問費',
        '管理服務費',
        '滯納金',
        '轉房費',
        '換約費',
        '轉換承租人',
        '維修費',
        '車馬費',
        '放鳥費',
        '管理費',
        '清潔費',
        '瓦斯費',
        '磁扣費',
        '水雜費',
        '垃圾費',
        '第四台',
        '鍋爐費',
        '租金',
        '電費',
    ],

    'debt_collection_delay_days' => 4,



    'view' => [
        // Show records in every index.blade.php.
        'default_records_in_index_blade' => 200,
    ]
];
