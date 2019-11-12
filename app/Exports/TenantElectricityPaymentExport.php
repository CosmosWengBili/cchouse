<?php

namespace App\Exports;

use App\Room;
use App\TenantContract;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TenantElectricityPaymentExport implements FromCollection, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $rooms = Room::where('room_layout', '<>', '公區')
                    ->with('building')
                    ->orderBy('rooms.building_id')
                    ->get();

        return $rooms->map(function ($room) {
            $contract = $room->activeContracts()->first();
            $building = $room->building;

            $prev110v = $room->current_110v;
            $prev220v = $room->current_220v;

            $buildingCode = $building ? $building->building_code : '';
            $roomNumber = $room->room_number;

            return [
                strval($buildingCode),
                strval($roomNumber),
                strval($prev220v),
                strval($prev110v),
                '',
                '',
                ''
            ];
        });
    }

    public function headings(): array
    {
        return ['物件代碼', '房號', '前期 110v', '前期 220v',	'本期 110v', '本期 220v', '抄表日'];
    }
}
