<?php

namespace App\Services;

use App\Services\RoomService;
use App\Building;

class BuildingService
{
    // protected $buildingRepository;

    // public function __construct() {
    //     $this->$buildingRepository = $buildingRepository;
    // }

    // make a building without saving
    public static function make($data)
    {
        return Building::make($data);
    }

    // 1. make a building, and save it
    // 3. create a empty room
    // 4. link the room to the building
    public static function create($data)
    {
        $building = Building::create($data);
        $emptyRoom = RoomService::makeEmptyRoom();
        static::hasRooms($building, $emptyRoom);
        return $building;
    }

    // set foreign key(s) of the given room(s)
    public static function hasRooms($building, $rooms)
    {
        if (!is_array($rooms)) {
            $rooms = [$rooms];
        }
        $building->rooms()->saveMany($rooms);
    }
}
