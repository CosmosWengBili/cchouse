<?php

namespace App\Notifications\Formatters;

class TextNotifyFormatter extends BaseFormatter
{
    public static function canFormat($notification): bool
    {
        return $notification->type == 'App\Notifications\TextNotify' ||
                $notification->type == 'App\Notifications\LandlordUpdated' ||
                $notification->type == 'App\Notifications\LandlordContractDue' ||
                $notification->type == 'App\Notifications\RoomHasChanged';
    }

    public function header(): string
    {
        return '一般通知';
    }

    public function content(): string
    {
        return $this->notification->data['content'];
    }
}
