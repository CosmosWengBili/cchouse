<?php

namespace App\Services;

use App\KeyRequest;
use App\User;
use App\Notifications\KeyRequestFinished;
use App\Notifications\LandlordUpdated;
use App\Notifications\ReceiptUpdated;

class NotificationService
{
    public static function notifyKeyRequestFinished($key_id)
    {
        $keyRequest = KeyRequest::where([
            'key_id' => $key_id,
            'status' => '預約中'
        ])
            ->orderBy('created_at', 'asc')
            ->first();
        $keyRequest->requestUser->notify(new KeyRequestFinished($keyRequest));
    }
    
    public static function notifyLandlordUpdated($model, $key)
    {
        $users = User::group('帳務組')->get();
        foreach( $users as $user ){
            $user->notify(new LandlordUpdated($model, $key));
        }
    }

    public static function notifyReceiptUpdated($model)
    {
        User::first()->notify(new ReceiptUpdated($model));
    }
}
