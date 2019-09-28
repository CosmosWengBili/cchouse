<?php
namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

trait ExtraInfo {
    public function scopeWithExtraInfo(Builder $builder) {
        $tableName = $this->getTable();

        return $this->joinRequiredTable($builder)->groupBy("{$tableName}.id");
    }

    public static function extraInfoColumns() {
        return (new self())->getExtraSelects();
    }

    private function joinRequiredTable(Builder $builder) {
        $tableName = $this->getTable();

        switch ($tableName) {
            case 'landlord_contracts':
                return $builder
                        ->join('buildings', 'buildings.id', '=', "{$tableName}.building_id")
                        ->join('rooms', 'buildings.id', '=', 'rooms.building_id');
                break;
            case 'tenant_contract':
            case 'keys':
                return $builder
                        ->join('rooms', 'rooms.id', '=', "{$tableName}.room_id")
                        ->join('buildings', 'buildings.id', '=', 'rooms.building_id')
                        ->join('landlord_contracts', 'landlord_contracts.building_id', '=', 'rooms.building_id');
                break;
            case 'debt_collections':
            case 'maintenances':
                return $builder
                        ->join('tenant_contract', 'tenant_contract.id', '=', "{$tableName}.tenant_contract_id")
                        ->join('rooms', 'rooms.id', '=', "tenant_contract.room_id")
                        ->join('buildings', 'buildings.id', '=', 'rooms.building_id')
                        ->join('landlord_contracts', 'landlord_contracts.building_id', '=', 'rooms.building_id');
                break;
            default:
                return $builder;
        }
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
            case 'tenant_contract':
            case 'keys':
            case 'debt_collections':
            case 'maintenances':
                $extraSelects = [
                    'landlord_contracts.commission_type AS commission_type',
                    'buildings.building_code AS building_code',
                    'buildings.title AS building_title',
                    'CONCAT(buildings.city, buildings.district, address) AS building_location',
                    'rooms.room_number AS room_number',
                    'rooms.room_status AS room_status',
                ];
                break;
            default:
                $extraSelects = [];
        }

        return $extraSelects;
    }
}
