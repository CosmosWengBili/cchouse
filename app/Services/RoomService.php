<?php

namespace App\Services;

use App\Room;
use App\Appliance;

class RoomService{

    public static function make($data) {
        return Room::make($data);
    }

    public static function create($data, $appiances) {

        $roomId = Room::insertGetId($data);
        $room = Room::find($roomId);
        $new_appliances = [];
        // generates all of appliances
        foreach ($appiances as $appiance) {
            $new_appliance = Appliance::make([
                    'subject'       => $appiance['subject'],
                    'spec_code'     => $appiance['spec_code'],
                    'count'        => $appiance['count'],
                    'vendor'        => $appiance['vendor'],
                    'maintenance_phone'  => $appiance['maintenance_phone'],
                    'comment'        => $appiance['comment']       
            ]);
            array_push($new_appliances, $new_appliance);
        }
            
        // link these appliances to the newly created room
        $room->appliances()->saveMany($new_appliances);
        return $room;
    }

    public static function update($room, $data, $appliances) {
        
        $keepIds = array_map(function ($appliance) {
            return isset($appliance['id'])
                ? $appliance['id']
                : null;
        }, $appliances);

        // remove removed item
        $room
            ->appliances()
            ->whereNotIn('id', $keepIds)
            ->delete();

        foreach ($appliances as $appliance) {
            if (isset($appliance['id'])) {
                $id = $appliance['id'];
                $db_appliance = $room->appliances()->find($id);
                $db_appliance->update($appliance);
            } else {
                Appliance::create(
                    array_merge($appliance, [
                        'room_id' => $room->id
                    ])
                );
            }
        }

        
        return $room->update($data);
    }

    // alias to make, but do it with custom data
    public static function makeEmptyRoom() {
        return Room::make([
            'room_code' => '公用',
        ]);
    }

    public function belongsToBuilding($room, $building) {
        $building->rooms()->save($room);
    }
}
