<?php

namespace App\Services;

use App\KeyRequest;
use App\Notifications\KeyRequestFinished;

class NotificationService
{
    public static function notifyKeyRequestFinished($key_id)
    {
        $keyRequest = KeyRequest::where([
            'key_id' => $key_id,
            'status' => 'reserved'
        ])
            ->orderBy('created_at', 'asc')
            ->first();
        $keyRequest->requestUser->notify(new KeyRequestFinished($keyRequest));
    }
}
