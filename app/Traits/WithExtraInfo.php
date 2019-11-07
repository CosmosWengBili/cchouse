<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\DB;
use function foo\func;

trait WithExtraInfo
{
    public function scopeWithExtraInfo(Builder $builder)
    {
        $tableName = $this->getTable();

        return $this->joinRequiredTable($builder)->groupBy("{$tableName}.id");
    }

    public static function extraInfoColumns()
    {
        return (new self())->getExtraSelects();
    }

    private function joinRequiredTable(Builder $builder)
    {
        $tableName = $this->getTable();

        switch ($tableName) {
            case 'buildings':
                return $builder
                        ->join('landlord_contracts', 'landlord_contracts.building_id', '=', 'buildings.id')
                        ->join('rooms', 'buildings.id', '=', 'rooms.building_id');
                break;
            case 'landlord_contracts':
                return $builder
                        ->join('buildings', 'buildings.id', '=', "{$tableName}.building_id")
                        ->join('rooms', 'buildings.id', '=', 'rooms.building_id');
                break;
            case 'tenant_contract':
                return $builder
                    ->join('rooms', 'rooms.id', '=', "{$tableName}.room_id")
                    ->join('tenants', 'tenants.id', '=', "{$tableName}.tenant_id")
                    ->join('buildings', 'buildings.id', '=', 'rooms.building_id')
                    ->join('landlord_contracts', 'landlord_contracts.building_id', '=', 'rooms.building_id');
            case 'keys':
            case 'landlord_payments':
                return $builder
                        ->join('rooms', 'rooms.id', '=', "{$tableName}.room_id")
                        ->join('buildings', 'buildings.id', '=', 'rooms.building_id')
                        ->join('landlord_contracts', 'landlord_contracts.building_id', '=', 'rooms.building_id');
                break;
            case 'maintenances':
                    return $builder
                    ->join('rooms', 'rooms.id', '=', 'maintenances.room_id')
                    ->join('buildings', 'buildings.id', '=', 'rooms.building_id')
                    ->join('landlord_contracts', 'landlord_contracts.building_id', '=', 'rooms.building_id');
                break;
            case 'debt_collections':
            case 'deposits':
                return $builder
                    ->Leftjoin('tenant_contract', 'tenant_contract.id', '=', "{$tableName}.tenant_contract_id")
                    ->join('rooms', 'rooms.id', '=', 'tenant_contract.room_id')
                    ->join('buildings', 'buildings.id', '=', 'rooms.building_id')
                    ->join('landlord_contracts', 'landlord_contracts.building_id', '=', 'rooms.building_id');
                break;
            case 'company_incomes':
                return $builder
                        ->leftJoin('tenant_contract', function ($join) use ($tableName) {
                            $join->on('tenant_contract.id', '=', "{$tableName}.incomable_id")
                                 ->on("{$tableName}.incomable_type", '=', DB::raw("'App\TenantContract'"));
                        })
                        ->leftJoin('rooms', 'rooms.id', '=', 'tenant_contract.room_id')
                        ->leftJoin('buildings', 'buildings.id', '=', 'rooms.building_id')
                        ->leftJoin('landlord_contracts', 'landlord_contracts.building_id', '=', 'rooms.building_id')
                        ->leftJoin('receipts', function (JoinClause $query) {
                            $query->on('company_incomes.id', '=', 'receipts.receiptable_id')
                                  ->where('receipts.receiptable_type', '=', 'App\CompanyIncome');
                        });
                break;
            default:
                return $builder;
        }
    }

    private function getExtraSelects()
    {
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
            case 'buildings':
                $extraSelects = [
                    'GROUP_CONCAT(landlord_contracts.commission_type) AS commission_type',
                    'GROUP_CONCAT(rooms.room_number) AS room_number',
                    'GROUP_CONCAT(rooms.room_status) AS room_status',
                ];
                break;
            case 'tenant_contract':
                $extraSelects = [
                    'tenants.name AS tenant_name',
                    'landlord_contracts.commission_type AS commission_type',
                    'buildings.building_code AS building_code',
                    'buildings.title AS building_title',
                    'CONCAT(buildings.city, buildings.district, address) AS building_location',
                    'rooms.room_number AS room_number',
                    'rooms.room_status AS room_status',
                ];
                break;
            case 'keys':
            case 'debt_collections':
            case 'maintenances':
            case 'landlord_payments':
                $extraSelects = [
                    'landlord_contracts.commission_type AS commission_type',
                    'buildings.building_code AS building_code',
                    'buildings.title AS building_title',
                    'CONCAT(buildings.city, buildings.district, address) AS building_location',
                    'rooms.room_number AS room_number',
                    'rooms.room_status AS room_status',
                ];
                break;
            case 'company_incomes':
                $extraSelects = [
                    'landlord_contracts.commission_type AS commission_type',
                    'buildings.building_code AS building_code',
                    'buildings.title AS building_title',
                    'CONCAT(buildings.city, buildings.district, address) AS building_location',
                    'rooms.room_number AS room_number',
                    'rooms.room_status AS room_status',
                    'GROUP_CONCAT(DISTINCT(receipts.invoice_serial_number)) AS invoice_serial_number',
                ];
                break;
            case 'deposits':
                $extraSelects = [
                    'buildings.building_code AS building_code',
                    'landlord_contracts.commission_type AS commission_type',
                    'rooms.room_number AS room_number',
                    'buildings.title AS building_title',
                ];

                break;

            default:
                $extraSelects = [];
        }

        return $extraSelects;
    }
}
