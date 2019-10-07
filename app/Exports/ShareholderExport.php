<?php
/**
 * Created by PhpStorm.
 * User: lichengxiu
 * Date: 2019-10-05
 * Time: 13:20
 */

namespace App\Exports;

use App\Exports\Sheets\ShareholderTotalSheet;
use App\LandlordContract;
use App\Services\MonthlyReportService;
use Carbon\Carbon;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ShareholderExport implements WithMultipleSheets
{
    /** @var Carbon $date */
    private $date;
    /** @var int $month */
    private $month;
    /** @var int $year */
    private $year;

    private $transferFroms;

    public function __construct(Carbon $date)
    {
        $this->date = $date;
        $this->year = $date->year;
        $this->month = $date->month;

        $this->transferFroms = [];
    }

    /**
     * @return array
     */
    public function sheets() :array
    {
        /** @var array $excelData */
        $excelData = $this->getExcelDataRows();

        // count row total amount
        $sheets[] = new ShareholderTotalSheet($this->year, $this->month, $excelData);

        $transferFroms = collect($this->transferFroms)->unique();
        foreach ($transferFroms as $transferFrom) {
            $sheets[] = new ShareholderTotalSheet($this->year, $this->month, $excelData, $transferFrom);
        }

        return $sheets;
    }

    private function getExcelDataRows()
    {
        $databaseData = $this->getDataFromDataBase();

        return $this->generateExcelData($databaseData);
    }

    private function generateExcelData(array $databaseData)
    {
        $excelData = [];
        foreach ($databaseData as $row) {
            // collect the transfer from for sheets
            $this->transferFroms[] = $row->transfer_from;

            $location = $row->city . $row->district . $row->address;
            $money = $this->getDataFromMonthlyReport(
                LandlordContract::find($row->landlord_contract_id)->first(),
                $this->month,
                $this->year
            );

            // 實收金額
            $actual_money = $row->carry_forward > 0 ? $row->carry_forward : 0;
            // 應收金額
            $amount_receivable = $row->carry_forward < 0 ? ~$row->carry_forward + 1 : 0;

            $excelData[] = [
                '', //$row->group,                                  // 組別
                $row->commission_type,                              // 物件屬性
                $row->building_code,                                // 物件代碼
                $location,                                          // 物件地址
                'carry_forward' => $row->carry_forward,             // 月結單金額
                'actual_money' => $actual_money,                    // 實收金額
                $row->name,                                         // 業主
                'money' => $money,                                  // 應付業主 按照 monthly_service 產生 shareholder 負擔費用的邏輯
                'method' => $row->method,                           // 方式
                $row->transfer_from,                                // 匯出銀行
                'amount_receivable' => $amount_receivable,          // 應收金額 < 0 然後顯示的數字要乘於 -1
                'exchange_fee' => $row->exchange_fee,               // 匯費
                $row->comment,                                      // 備註
                $row->bank_name . $row->bank_branch,                // 銀行/分行
                $row->bank_code,                                    // 銀行/分行代碼
                $row->account_name,                                 // 戶名（受款人）
                $row->account_number,                               // 帳號
                $row->contact_method,                               // 聯絡方式
                $row->bill_delivery,                                // 郵寄/傳真
            ];
        }

        return $excelData;
    }

    private function getDataFromMonthlyReport(LandlordContract $landlord_contract, $month, $year)
    {
        $data = (new MonthlyReportService())->getMonthlyReport($landlord_contract, $month, $year);
        return collect($data->toArray()['shareholders'])->reduce(function ($carry, $item) {
            return $carry + $item['distribution_fee'];
        }, 0);
    }

    /**
     * 從資料庫內取出會用到的數據
     * @return array
     */
    private function getDataFromDataBase()
    {
        return DB::table('building_shareholder')
            ->join('shareholders', 'building_shareholder.shareholder_id', '=', 'shareholders.id')
            ->join('buildings', 'building_shareholder.building_id', '=', 'buildings.id')
            ->join('monthly_reports', function (JoinClause $query) {
                $query->leftJoin('landlord_contracts', 'landlord_contracts.id', '=', 'monthly_reports.landlord_contract_id');
                $query->on('monthly_reports.landlord_contract_id', '=', 'landlord_contracts.id');
                $query->on('buildings.id', '=', 'landlord_contracts.building_id');
            })
            ->get()
            ->toArray();
    }
}
