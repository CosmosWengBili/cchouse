<?php

namespace App\Observers;

use App\Services\NotificationService;
use App\LandlordContract;

class LandlordContractObserver
{
    public function updating(LandlordContract $landlordContract)
    {
      $initLandlordContract = $landlordContract->getOriginal();
      $afterLandlordContract = $landlordContract->getAttributes();
      foreach( $initLandlordContract as $key => $value ){
        if( in_array($key, ['is_notarized', 'commissioner_id']) ){
            continue;
        }
        if( $afterLandlordContract[$key] != $value){
            $rooms = $landlordContract->building->rooms;
            foreach( $rooms as $room ){
                if ( !empty($room->activeContracts()) ){
                    NotificationService::notifyLandlordUpdated(
                        $landlordContract, $key
                    );
                    break;
                };
            }
        }
      }
    }
}
