<?php

namespace App\Imports;

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
use App\TenantElectricityPayment;
use App\LandlordOtherSubject;
use App\Services\InvoiceService;

class InvoiceImport implements ToModel, WithHeadingRow
{
    private $row;
    public function __construct()
    {
      $this->row = 1;
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
          if( $row['資料來源(程式用)'] == "landlord_other_subjects" ){
              $landlord_names = $model->room->building->activeContracts()->landlords->pluck('name');
              $receiped_landlord_names = $model->receipts->pluck('receiver');
              $receipt->receiver = $landlord_names->diff($receiped_landlord_names)->first();
          }
          else{
              $receipt->receiver = $service->fetchInvoiceReceiver($model);
          }
          $model->receipts()->save($receipt);
      }
      else{
          $receipt = Receipt::find($row['發票ID(程式用)']);

          // If the invoice_serial_number from user is different with receipt, notify invoice group users
          if( $receipt->invoice_serial_number != $row['發票號碼']){
              NotificationService::notifyReceiptUpdated($model);
          }
          $receipt->invoice_serial_number = $row['發票號碼'];
          $receipt->date = $row['發票日期'];
          $receipt->invoice_price = $row['發票金額'];
          $receipt->comment = $row['備註'];
          if(isset($row['收取者'])){
            $receipt->receiver = $row['收取者'];
          }
          $receipt->save();
      }          
    }
}
