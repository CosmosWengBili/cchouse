<?php 

namespace App\Exports;

use App\Exports\Sheets\ShareholderTotalSheet;
use App\LandlordContract;
use App\MonthlyReport;
use App\Services\MonthlyReportService;
use Carbon\Carbon;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

class ShareholderExport implements WithMultipleSheets, WithCustomValueBinder
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
        $sheets[] = new ShareholderTotalSheet($this->year, $this->month, $excelData['global']);

        $transferFroms = collect($this->transferFroms)->unique();
        foreach ($transferFroms as $transferFrom) {
            $sheets[] = new ShareholderTotalSheet($this->year, $this->month, $excelData[$transferFrom], $transferFrom);
        }

        return $sheets;
    }

    private function getExcelDataRows()
    {
        $databaseShareholderData = $this->getDataFromDataBase('shareholder');
        $databaseLandlordData = $this->getDataFromDataBase('landlord');

        return $this->generateExcelData($databaseShareholderData, $databaseLandlordData);
    }

    private function generateExcelData(array $databaseShareholderData, $databaseLandlordData)
    {
        $excelData = [];
        foreach ($databaseShareholderData as $row) {
            // collect the transfer from for sheets
            $this->transferFroms[] = $row->transfer_from;

            $location = $row->city . $row->district . $row->address;
            $money = $this->getShareholderMoney($row);

            // 實收金額
            $actual_money = $row->carry_forward > 0 ? $row->carry_forward : 0;
            // 應收金額
            $amount_receivable = $row->carry_forward < 0 ? ~$row->carry_forward + 1 : 0;

            $data = [
                $row->commission_type,                              // 物件屬性
                $row->building_code,                                // 物件代碼
                $location,                                          // 物件地址
                'carry_forward' => $row->carry_forward,             // 月結單金額
                'actual_money' => $actual_money,                    // 實收金額
                $row->name,                                         // 業主
                'money' => $money,                                    // 應付業主
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

            $excelData['global'][] = $data; 
            $excelData[$row->transfer_from][] = $data; 
        }
        foreach ($databaseLandlordData as $row) {
            // collect the transfer from for sheets
            $this->transferFroms[] = '玉山兆基';
            $money = MonthlyReport::where('landlord_contract_id', $row->id)
                                ->where('year', $this->year)
                                ->where('month', $this->month)
                                ->get()
                                ->first()['carry_forward'] ?? 0;
            

            // 實收金額
            $actualMoney = $money > 0 ? $money : 0;
            // 應收金額
            $amountReceivable = $money < 0 ? ~$money + 1 : 0;

            $landlordNames = implode(',', $row->landlords->pluck('name')->toArray());
            $landlordBankCodes = implode(',', $row->landlords->pluck('bank_code')->toArray());
            $landlordBranchCodes = implode(',', $row->landlords->pluck('branch_code')->toArray());
            $landlordAccountNames = implode(',', $row->landlords->pluck('account_name')->toArray());
            $landlordAccountNumbers = implode(',', $row->landlords->pluck('account_number')->toArray());
            $contactMethods = implode(',', $row->landlords->map(function($landlord, $key){
                return implode(',',$landlord->phones->pluck('value')->toArray());
            })->toArray());
            $billDeliverys = implode(',', $row->landlords->map(function($landlord, $key){
                return implode(',',$landlord->mailingAddresses->pluck('value')->toArray());
            })->toArray());

            $data = [
                $row->commission_type,                              // 物件屬性
                $row->building->building_code,                      // 物件代碼
                $row->building->location,                           // 物件地址
                'carry_forward' => $money,                          // 月結單金額
                'actual_money' => $actualMoney,                     // 實收金額
                $landlordNames,                                     // 業主
                'money' => $money,                                  // 應付業主
                'method' => '7系列 - 匯款',                          // 方式
                '玉山兆基',                                          // 匯出銀行
                'amount_receivable' => $amountReceivable,           // 應收金額 < 0 然後顯示的數字要乘於 -1
                'exchange_fee' => 0,                                // 匯費
                '',                                                 // 備註
                '',                                                 // 銀行/分行
                $landlordBankCodes . $landlordBranchCodes,          // 銀行/分行代碼
                $landlordAccountNames,                              // 戶名（受款人）
                $landlordAccountNumbers,                            // 帳號
                $contactMethods,                                    // 聯絡方式
                $billDeliverys,                                     // 郵寄/傳真
            ];   
            
            $excelData['global'][] = $data; 
            $excelData['玉山兆基'][] = $data;
        }
        return $excelData;
    }

    private function getDataFromMonthlyReport(LandlordContract $landlord_contract, $month, $year)
    {
        $data = (new MonthlyReportService())->getShareholdersInfo($landlord_contract, $month, $year);
        return collect($data)->reduce(function ($carry, $item) {
            return $carry + $item['distribution_fee'];
        }, 0);
    }

    /**
     * 從資料庫內取出會用到的數據
     * @return array
     */
    private function getDataFromDataBase($model)
    {
        if( $model == 'shareholder' ){
            return DB::table('building_shareholder')
            ->join('shareholders', 'building_shareholder.shareholder_id', '=', 'shareholders.id')
            ->join('buildings', 'building_shareholder.building_id', '=', 'buildings.id')
            ->join('monthly_reports', function (JoinClause $query) {
                $query->leftJoin('landlord_contracts', 'landlord_contracts.id', '=', 'monthly_reports.landlord_contract_id');
                $query->on('monthly_reports.landlord_contract_id', '=', 'landlord_contracts.id');
                $query->on('buildings.id', '=', 'landlord_contracts.building_id');
                $query->where('landlord_contracts.commission_start_date', '<', Carbon::today());
                $query->where('landlord_contracts.commission_end_date', '>', Carbon::today());
            })
            ->select(DB::raw('*, shareholders.distribution_method AS shareholder_distribution_method'))
            ->where('shareholders.distribution_end_date', '>', Carbon::today())
            ->where('shareholders.deleted_at', null)
            ->where('buildings.deleted_at', null)
            ->where('monthly_reports.deleted_at', null)
            ->get()
            ->toArray();
        }
        else if( $model == 'landlord' ){
            return LandlordContract::where('commission_type', '代管')
                            ->where('commission_start_date', '<', Carbon::today())
                            ->where('commission_end_date', '>', Carbon::today())
                            ->with(['building', 'landlords'])
                            ->get();
                        
        }

    }

    private function getShareholderMoney($shareholder){
        if( $shareholder->shareholder_distribution_method == '固定' ){
            return $shareholder->distribution_amount;
        }
        else if( $shareholder->shareholder_distribution_method == '浮動' ){
            $revenue = MonthlyReport::where('landlord_contract_id', $shareholder->landlord_contract_id)
                                    ->where('year', $this->year)
                                    ->where('month', $this->month)
                                    ->get()
                                    ->first()['carry_forward'] ?? 0;
            if($revenue < 0){ $revenue = 0; }
            return round($shareholder->distribution_rate * $revenue / 100);
        }
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
