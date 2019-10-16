<?php

namespace App\Notifications\Formatters;

class TextNotifyFormatter extends BaseFormatter {

    static function canFormat($notification): bool
    {
        return $notification->type == 'App\Notifications\TextNotify' ||
               $notification->type == 'App\Notifications\LandlordUpdated' ;
    }

    function header(): string
    {
        return '一般通知';
    }

    function content(): string
    {
        return $this->notification->data['content'];
    }
}
