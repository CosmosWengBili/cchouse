<?php
namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

trait ExtraInfo {
    public function scopeExtraInfo(Builder $builder) {
        $tableName = $this->getTable();

        return $builder
            ->join('buildings', "{$tableName}.building_id", '=', "buildings.id")
            ->join('rooms', 'buildings.id', '=', "rooms.building_id")
            ->groupBy("{$tableName}.id");
    }

    public static function extraInfoColumns() {
        return (new self())->getExtraSelects();
    }

    private function getExtraSelects() {
        $tableName = $this->getTable();

        switch ($tableName) {
            case 'landlord_contracts':
                $extraSelects = [
                    'landlord_contracts.commission_type AS commission_type',
                    'buildings.building_code AS building_code',
                    'buildings.title AS building_title',
                    'CONCAT(buildings.city, buildings.district, address) AS building_location',
                    'GROUP_CONCAT(rooms.room_number) AS room_number',
                    'GROUP_CONCAT(rooms.room_status) AS room_status',
                ];

                break;
            default:
                $extraSelects = [];
        }

        return $extraSelects;
    }

}
