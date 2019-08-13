<?php

namespace App\Services;

use App\Room;

class RoomService{

    public static function make($data) {
        return Room::make($data);
    }

    public static function create($data) {
        return Room::create($data);
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
