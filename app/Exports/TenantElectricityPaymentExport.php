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
        $rooms = Room::where('room_layout', '<>', '公用')
                    ->with('building')
                    ->orderBy('rooms.building_id')
                    ->get();

        return $rooms->map(function ($room) {
            $contract = $room->activeContracts()->first();
            $building = $room->building;
            $prev110v = null;
            $prev220v = null;

            if (is_null($contract)) {
                $prev110v = $room->current_110v;
                $prev220v = $room->current_220v;
            } else {
                $payment = $contract->tenantElectricityPayments()
                                    ->where('due_time', '<', Carbon::now())
                                    ->orderBy('due_time', 'desc')
                                    ->first(); // 上期 tenantElectricityPayments
                if (is_null($payment)) { // 第一期
                    $prev110v = $contract->{'110v_start_degree'};
                    $prev220v = $contract->{'220v_start_degree'};
                } else {
                    $prev110v = $payment->{'110v_end_degree'};
                    $prev220v = $payment->{'220v_end_degree'};
                }
            }

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
