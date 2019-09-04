<?php

namespace App\Notifications\Formatters;

class NullFormatter extends BaseFormatter {

    static function canFormat($notification): bool
    {
        return false;
    }

    function header(): string
    {
        return '未定義訊息';
    }

    function content(): string
    {
        return json_encode($this->notification->data);
    }
}
