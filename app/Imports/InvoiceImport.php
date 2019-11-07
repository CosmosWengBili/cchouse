<?php

namespace App\Imports;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use App\ComanyIncome;
use App\PayLog;
use App\Receipt;
use App\Maintenance;
use App\TenantPayment;
use App\EditorialReview;
use App\TenantElectricityPayment;
use App\LandlordOtherSubject;
use App\Services\InvoiceService;
use App\Services\NotificationService;

class InvoiceImport implements ToModel, WithHeadingRow
{
    private $result;
    public function __construct()
    {
      $this->result = ['status'=>'success', 'msg' => ''];
    }

    /**
     * @param array $row
     *
     * @return Model|null
     */
    public function model(array $row)
    {
        $service = new InvoiceService();
        $model = app("App\\".studly_case(Str::singular($row['資料來源(程式用)'])))->find($row['來源資料編號']);
        if(isset($model)){
            if ($row['發票ID(程式用)'] == null) {
                // Check whether invoice_serial_number exist to avoid generating redundant receipt
                if( $row['發票號碼'] == '' ){
                    return ;
                }
                $receipt = new Receipt();
                $receipt->invoice_serial_number = $row['發票號碼'];
                $receipt->date = $row['發票日期'];
                $receipt->invoice_price = $row['發票金額'];
                $receipt->comment = $row['備註'];
                $model->receipts()->save($receipt);
            }
            else{
                $receipt = Receipt::find($row['發票ID(程式用)']);
      
                // If the invoice_serial_number from user is different with receipt, notify invoice group users
                if( $receipt->invoice_serial_number != $row['發票號碼']){
                    NotificationService::notifyReceiptUpdated($model);
                    EditorialReview::create([
                        'editable_id' => $receipt->id,
                        'editable_type' => get_class($receipt),
                        'original_value' => $receipt->getAttributes(),
                        'edit_value' => [
                            'invoice_serial_number' => $row['發票號碼'],
                            'date' => $row['發票日期'],
                            'invoice_price' => $row['發票金額'],
                            'comment' => $row['備註'],
                        ],
                        'edit_user' => Auth::id(),
                        'extra_data' => '',
                        'comment' => '發票號碼更新',
                    ]);  
                }
                else{
                    $receipt->invoice_serial_number = $row['發票號碼'];
                    $receipt->date = $row['發票日期'];
                    $receipt->invoice_price = $row['發票金額'];
                    $receipt->comment = $row['備註'];
                    $receipt->save();
                }
            }    
        }
        else{
            $this->result['status'] = 'error';
            $this->result['msg'] .= $row['費用來源'].'編號為 '.$row['來源資料編號'].' ，且存入日期為'.$row['存入日期'].'的發票資料來源有誤' ;
        } 
    }

    public function getResult(): array
    {
        return $this->result;
    }
}
