<?php

namespace App\Imports;

use App\Room;
use App\Building;
use App\TenantElectricityPayment;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;

class TenantElectricityPaymentImport implements ToModel, WithHeadingRow, WithCustomValueBinder
{
    /**
     * @var int
     */
    private $row;

    /**
     * @param array $row
     *
     * @return Model|null
     */
    public function __construct()
    {
        $this->row = 1;
    }

    public function model(array $row)
    {
        $now = Carbon::now();
        $buildingCode = $row['物件代碼'];
        $roomNumber = $row['房號'];
        if (is_null($buildingCode) && is_null($roomNumber)) { // empty row
            return null;
        }

        $prev110 = $row['前期 110v'];
        $prev220 = $row['前期 220v'];
        $this110 = $row['本期 110v'];
        $this220 = $row['本期 220v'];
        if ($this110 < $prev110 || $this220 < $prev220) throw new Exception("物件代碼: {$buildingCode}, 房號: {$roomNumber} 電費資料有誤");

        try {
            $ammeterReadDate = Date::excelToDateTimeObject($row['抄表日']);
        } catch (\Throwable $th) {
            throw new Exception("Row: {$this->row}, 無效的抄表日格式");
        }
        $ammeterReadMonth = intval($ammeterReadDate->format('m'), 10);

        // 從 $buildingCode 和 $roomNumber 取得 room
        $room = Building::where('building_code', $buildingCode)
                        ->first()
                        ->normalRooms()
                        ->where('room_number', $roomNumber)
                        ->first();
        if (is_null($room)) throw new Exception("物件代碼: {$buildingCode}, 房號: {$roomNumber} 找不到對應的房間");

        // 從 room 取得 contract
        $contract = $room->activeContracts()->first();
        if (is_null($contract)){
            $room->update(['current_110v' => $this110, 'current_220v' => $this220]);
        }
        else{
            // 計算電費
            $pricePerDegree = $contract->electricity_price_per_degree;
            $pricePerDegreeSummer = $contract->electricity_price_per_degree_summer;
            $ratio = in_array($ammeterReadMonth, [7, 8, 9, 10]) ? $pricePerDegreeSummer : $pricePerDegree;
            $amount = round(($this110 + $this220 - $prev110 - $prev220) * $ratio);

            // 計算 dueTime
            $rentPayDay = $contract->rent_pay_day;
            $dueTime = Carbon::create($ammeterReadDate->format('Y-m-d'))->setDay($rentPayDay)->addMonth(1);
            $data = [
                'tenant_contract_id' => $contract->id,
                'ammeter_read_date' => $ammeterReadDate,
                '110v_start_degree' => $prev110,
                '110v_end_degree' => $this110,
                '220v_start_degree' => $prev220,
                '220v_end_degree' => $this220,
                'amount' => $amount,
                'due_time' => $dueTime,
                'is_charge_off_done' => false,
                'charge_off_date' => '',
                'comment' => '',
                'is_pay_off' => false
            ];
            $this->row += 1;
            DB::transaction(function () use ($room, $this110, $this220, $data) {
                $room->update(['current_110v' => $this110, 'current_220v' => $this220]);
                TenantElectricityPayment::create($data);
            });
        }

        
        return null;
    }

    /**
     * Bind value to a cell.
     *
     * @param Cell $cell Cell to bind value to
     * @param mixed $value Value to bind in cell
     *
     * @return bool
     */
    public function bindValue(Cell $cell, $value)
    {
        $cell->setValueExplicit($value, DataType::TYPE_STRING);
        return true;
    }
}
