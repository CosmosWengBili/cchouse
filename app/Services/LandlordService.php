<?php

namespace App\Services;

use App\Landlord;
use App\Services\NotificationService;

class LandlordService
{
    public static function update($landlord, $data)
    {
        if( $data['is_legal_person'] != $landlord->is_legal_person){
            $rooms = $landlord->activeContracts()->building->rooms;
            foreach( $rooms as $room_key => $room ){
                if ( !empty($room->activeContracts()) ){
                    NotificationService::notifyLandlordIdentityUpdated(
                        $landlord
                    );
                    break;
                };
            }
        }

        return $landlord->update($data);
    }
}
