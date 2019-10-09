<?php

namespace App\Observers;

use App\Services\NotificationService;
use App\Landlord;

class LandlordObserver
{
    public function updating(Landlord $landlord)
    {
      $initLandlord = $landlord->getOriginal();
      $afterLandlord = $landlord->getAttributes();
      foreach( $initLandlord as $key => $value ){
        if( $key == 'birth' ){
            continue;
        }
        if( $afterLandlord[$key] != $value){
            $rooms = $landlord->activeContracts()->building->rooms;
            foreach( $rooms as $room ){
                if ( !empty($room->activeContracts()) ){
                    NotificationService::notifyLandlordUpdated(
                        $landlord, $key
                    );
                    break;
                };
            }
        }
      }
    }
}
