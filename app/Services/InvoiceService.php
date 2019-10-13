<?php

namespace App\Services;

use Carbon\Carbon;
use App\PayLog;
use App\TenantPayment;
use App\CompanyIncome;
use App\TenantContract;
use App\TenantElectricityPayment;
use App\LandlordContract;
use App\Maintenance;
use App\LandlordOtherSubject;
use App\Deposit;
use App\SystemVariable;
use App\Receipt;

class InvoiceService
{   
    public $invoice_count;
    public $global_data;
    public function makeInvoiceData($start_date, $end_date)
    {
        $this->global_data = [
            'tenant' => [], 'landlord' => [],  'company' => []  
        ];
        // Init pay logs data
        $pay_logs = PayLog::whereBetween('paid_at', [$start_date, $end_date])
            ->where('receipt_type', '=', '發票')
            ->whereIn('loggable_type', ['App\TenantPayment', 'App\TenantElectricityPayment'])
            ->orderBy('virtual_account', 'DESC')
            ->orderBy('paid_at', 'ASC')
            ->with([
                'loggable.tenantContract.tenant',
                'loggable.tenantContract.room.building.landlordContracts'
            ])
            ->get();

        // Init the variables which would be used inside invoice data for loop
        $this->invoice_count = 0;
        $invoice_item_count = 0;
        $payment_count = 0;
        $subtotal = 0;
        $comment = '';
        $current_tenant_contract_id = 0;

        // Init the variables which would be used to detect whether over landlord rent price
        $building_price_map = array();

        // Generate invoice data
        foreach ($pay_logs as $pay_log_key => $pay_log) {
            // Check whether the payments could be added to invoice data
            if (
                $pay_log->receipt_type == '收據'
            ) {
                continue;
            }

            $data = $this->makeInvoiceMockData();

            $invoice_item_count++;
            $pay_log_tenant_contract_id =
                $pay_log['loggable']['tenantContract']->id;

            // Update subtotal-use index
            if ($pay_log_tenant_contract_id != $current_tenant_contract_id) {
                $this->invoice_count++;
                $invoice_item_count = 1;
                $current_tenant_contract_id = $pay_log_tenant_contract_id;
                $payment_count = 0;

                $tenantContract = $pay_log->loggable->tenantContract;

                // Set payment count to know when to make subtotal row
                $payment_models = [
                    'App\TenantPayment',
                    'App\TenantElectricityPayment'
                ];
                foreach ($payment_models as $model_key => $model) {
                    $tmp_payments = $model
                        ::whereHas('payLogs', function ($q) use (
                            $pay_log_tenant_contract_id
                        ) {
                            $q->where(
                                'tenant_contract_id',
                                '=',
                                $pay_log_tenant_contract_id
                            );
                        })
                        ->when( $model_key == 'App\TenantPayment' , function ($query) {
                            return $query->where('collected_by', '公司');
                        });
                    
                    foreach ($tmp_payments->get() as $payment_key => $payment) {
                        $payment_count += $payment->payLogs
                            ->whereBetween('paid_at', [$start_date, $end_date])
                            ->where('receipt_type', '=', '發票')
                            ->count();
                    }
                }
            }

            // Set normal value
            $data['invoice_count'] = $this->invoice_count;
            $data['invoice_date'] = $pay_log->loggable->receipts()->first()['date'];
            $data['invoice_item_idx'] = $invoice_item_count;
            $data['invoice_item_name'] = $this->makeInvoiceItemName(
                $pay_log['loggable'],
                'payment'
            );
            $data['quantity'] = 1;
            $data['amount'] = $pay_log->amount;
            $data['tax_type'] = 1;
            $data['tax_rate'] = 0.05;

            // Set value for maping relative payment model
            $data['data_table'] = __('general.' . $pay_log->loggable->getTable()) .' , '. $pay_log->loggable->subject; 
            $data['data_table_id'] = $pay_log->loggable->id;
            $data['data_receipt_id'] = $pay_log->loggable->receipts()->first()['id'];

            $subtotal += $pay_log->loggable->amount;
            $comment =
                $comment .
                ($pay_log->loggable->comment .
                    ';' .
                    $pay_log->loggable->tenantContract->room->comment);

            // Make subtotal row

            if ($payment_count == $invoice_item_count) {
                $data['invoice_item_idx'] = $data['invoice_item_idx'];
                if (
                    $pay_log->loggable->tenantContract->tenant->is_legal_person
                ) {
                    $data['company_number'] =
                        $pay_log->loggable->tenantContract->tenant->certificate_number;
                    $data['company_name'] =
                        $pay_log->loggable->tenantContract->tenant->name;
                }
                $data['comment'] = $comment;
                $data['building_code'] =
                    $pay_log->loggable->tenantContract->room->building->building_code;
                $data['room_number'] =
                    $pay_log->loggable->tenantContract->room->room_number;
                $data['deposit_date'] = $pay_log->paid_at->format('Y-m-d');
                $data['actual_deposit_date'] = $pay_log->paid_at->format(
                    'Y-m-d'
                );
                $data['invoice_collection_number'] =
                    $pay_log->loggable->tenantContract->invoice_collection_number;
                $data['invoice_serial_number'] = $pay_log->loggable->receipts()->first()['invoice_serial_number'];
                $data['invoice_price'] = $subtotal;
                $data['subtotal'] = $subtotal;
                $subtotal = 0;
            }
            if( $pay_log->loggable->subject == "租金" ){
                array_push($this->global_data['company'], $data);
            }
            else{
                array_push($this->global_data['tenant'], $data);
            }
            
        }

        // Generate deposit interest, maintenance data and collection data
        $this->invoice_count ++;
        $deposit_interest_data = $this->makeDepositInterest($start_date, $end_date);
        $maintenance_data = $this->makeMaintenance($start_date, $end_date);
        $deposit_data = $this->makeDeposits($start_date, $end_date);
        $landlord_other_subject_data = $this->makeLandlordOtherSubjects($start_date, $end_date);
        
        return $this->global_data;
    }

    public function makeDepositInterest($startDate, $endDate)
    {

        $depositInterests = CompanyIncome::whereBetween('income_date', [$startDate, $endDate])
                                         ->where('subject', '押金設算息')
                                         ->get();

        // Genenate deposit interest data
        foreach ($depositInterests as $depositInterest) {

            $data = $this->makeInvoiceMockData();
            $data['invoice_date'] = $depositInterest->receipts()->first()['date'];
            $data['invoice_item_name'] = $depositInterest->subject;
            $data['amount'] = $depositInterest->amount;
            $data['data_table'] = $data['data_table'] =  __('general.' . $depositInterest->getTable()); 
            $data['data_table_id'] = $depositInterest->id;
            $data['data_receipt_id'] = $depositInterest->receipts()->first()['id'];

            $tenantContract = $depositInterest->incomable()->get()[0];
            if ($tenantContract->tenant->is_legal_person) {
                $data['company_number'] =
                    $tenantContract->tenant->certificate_number;
                $data['company_name'] = $tenantContract->tenant->name;
            }
            $data['comment'] = $depositInterest->comment;
            $data['building_code'] = $tenantContract->room->building->building_code;
            $data['room_number'] = $tenantContract->room->room_number;
            $data['deposit_date'] = $depositInterest->income_date->format('Y-m-d');
            $data['actual_deposit_date'] = $depositInterest->income_date->format('Y-m-d');
            $data['invoice_collection_number'] = $tenantContract->invoice_collection_number;
            $data['invoice_price'] = $depositInterest->amount;
            $data['invoice_serial_number'] = $depositInterest->receipts()->first()['invoice_serial_number'];

            array_push($this->global_data['tenant'], $data);
            $this->invoice_count ++;
        }
    }

    public function makeMaintenance($start_date, $end_date)
    {
        // Retrieve data from LandlordPayment not Maintenance 
        $maintenances = Maintenance::where('afford_by','房東')
                                    ->whereBetween('closed_date', [$start_date, $end_date])
                                    ->with(['tenantContract.room.building.landlordContracts'])
                                    ->get();

        // Genenate maintenance data
        foreach ($maintenances as $maintenance) {
            $landlords = $maintenance->tenantContract->room->building->landlordContracts->last()
                ->landlords;
            $initial_amount = $maintenance->price;
            $per_amount = round($initial_amount / $landlords->count());
            foreach ($landlords as $landlord_key => $landlord) {
                $data = $this->makeInvoiceMockData();
                $data['invoice_date'] = $maintenance->receipts()->first()['date'];
                $data['invoice_item_name'] = '管理服務費(維修費)';
                $data['amount'] = $per_amount;

                // Set value for maping relative payment model
                $data['data_table'] = __('general.' . $maintenance->getTable()); 
                $data['data_table_id'] = $maintenance->id;
                $data['data_receipt_id'] = $maintenance->receipts()->first()['id'];

                if ($landlord->is_legal_person) {
                    $data['company_number'] = $landlord->certificate_number;
                    $data['company_name'] = $landlord->name;
                }
                $data['comment'] = '';
                $data['building_code'] = $maintenance->tenantContract->room->building->building_code;
                $data['room_number'] = $maintenance->tenantContract->room->room_number;
                $data['deposit_date'] = $maintenance->closed_date;
                $data['actual_deposit_date'] = $maintenance->closed_date;
                $data['invoice_collection_number'] = $landlord->invoice_collection_number;
                $data['invoice_price'] = $per_amount;
                $data['invoice_serial_number'] = $maintenance->receipts()->first()['invoice_serial_number'];

                if( $landlord->id == $landlords->last()->id ){
                    $data['amount'] = $initial_amount - $per_amount*($landlords->count()-1);
                    $data['invoice_price'] = $initial_amount - $per_amount*($landlords->count()-1);
                }

                array_push($this->global_data['landlord'], $data);
                $this->invoice_count ++;
            }
        }
    }
    public function makeDeposits($start_date, $end_date)
    {
        $deposits = PayLog::whereBetween('paid_at', [$start_date, $end_date])
                            ->where('receipt_type', '=', '發票')
                            ->where('loggable_type', '=', 'App\Deposit')
                            ->orderBy('virtual_account', 'DESC')
                            ->orderBy('paid_at', 'ASC')
                            ->get();
        // Genenate deposit data
        foreach ($deposits as $deposit_key => $deposit) {
            $data = $this->makeInvoiceMockData();
            $data['invoice_date'] = $deposit->receipts()->first()['date'];
            if( $deposit->subject == "訂金(房東)" ){
                $data['invoice_item_name'] = '管理服務費';
            }
            else if( $deposit->subject == "訂金" ){
                $data['invoice_item_name'] = '違約金';
            }
            $data['amount'] = $deposit->amount;

            // Set value for maping relative payment model
            $data['data_table'] =  __('general.' . $deposit->getTable()); 
            $data['data_table_id'] = $deposit->id;
            $data['data_receipt_id'] = $deposit->receipts()->first()['id'];

            // ToDo: uncomment when Deposit been finished
            // if ($deposit->loggable->payer_is_legal_person) {
            //     $data['company_number'] = $deposit->loggable->payer_certification_number;
            //     $data['company_name'] = $deposit->loggable->payer_name;
            // }
            // $data['room_code'] = $deposit->loggable->room->room_code;
            // $data['room_number'] = $deposit->loggable->room->room_number;
            $data['building_code'] = '';
            $data['room_number'] = '';

            $data['deposit_date'] = $deposit->paid_at->format('Y-m-d');
            $data['actual_deposit_date'] = $deposit->paid_at->format('Y-m-d');
            $data['invoice_collection_number'] = '';
            $data['invoice_price'] = $deposit->amount;
            $data['invoice_serial_number'] = $deposit->receipts()->first()['invoice_serial_number'];

            array_push($this->global_data['tenant'], $data);
            $this->invoice_count ++;
        }
    }

    public function makeLandlordOtherSubjects($start_date, $end_date)
    {
        $landlord_other_subjects = LandlordOtherSubject::where('is_invoiced', true)
            ->whereBetween('expense_date', [
                $start_date,
                $end_date
            ])
            ->with(['room.building.landlordContracts'])
            ->get();

        // Genenate landlord_other_subject data
        foreach ($landlord_other_subjects as $landlord_other_subject) {
            $landlords = $landlord_other_subject->room->building->landlordContracts->last()->landlords;
            $initial_amount = $landlord_other_subject->amount;
            $per_amount = round($initial_amount / $landlords->count());
            foreach ($landlords as $landlord_key => $landlord) {
                $data = $this->makeInvoiceMockData();
                $data['invoice_date'] = $landlord_other_subject->receipts()->first()['date'];
                $data['invoice_item_name'] = $landlord_other_subject->invoice_item_name;
                $data['amount'] = $per_amount;

                // Set value for maping relative payment model
                $data['data_table'] =  __('general.' . $landlord_other_subject->getTable()); 
                $data['data_table_id'] = $landlord_other_subject->id;
                $data['data_receipt_id'] = $landlord_other_subject->receipts()
                                                                  ->where('receiver', $landlord->name)
                                                                  ->first()['id'];

                if ($landlord->is_legal_person) {
                    $data['company_number'] = $landlord->certificate_number;
                    $data['company_name'] = $landlord->name;
                }
                $data['building_code'] = $landlord_other_subject->room->building->building_code;
                $data['room_number'] = $landlord_other_subject->room->room_number;
                $data['deposit_date'] = $landlord_other_subject->expense_date;
                $data['actual_deposit_date'] = $landlord_other_subject->expense_date;
                $data['invoice_collection_number'] = $landlord->invoice_collection_number;
                $data['invoice_price'] = $per_amount;
                $data['invoice_serial_number'] = $landlord_other_subject->receipts()
                                                                        ->where('receiver', $landlord->name)
                                                                        ->first()['invoice_serial_number'];

                if( $landlord->id == $landlords->last()->id ){
                    $data['amount'] = $initial_amount - $per_amount*($landlords->count()-1);
                    $data['invoice_price'] = $initial_amount - $per_amount*($landlords->count()-1);
                }
                array_push($this->global_data['landlord'], $data);
                $this->invoice_count ++;
            }
        }
        
    }
    // Turn resources title to invoice item name
    public function makeInvoiceItemName($object, $type)
    {
        if ($type == 'payment') {
            if ($object['subject'] == '維修費') {
                return '管理服務費(維修費)';
            } elseif ($object['subject'] == '電費') {
                return '管理服務費(代收電費)';
            } elseif ($object['subject'] == '水雜費') {
                return '管理服務費(代收水費)';
            } elseif ($object['subject'] == '清潔費') {
                return '管理服務費(預收清潔費)';
            } elseif ($object['subject'] == '租金') {
                return '租金收入';
            } elseif ($object['subject'] == '設備扣款') {
                return '違約金';
            }elseif (
                in_array($object['subject'], ['轉房費', '換約費', '滯納金'])
            ) {
                return '行政手續費';
            } elseif (
                in_array($object['subject'], [
                    '管理服務費',
                    '服務費',
                    '垃圾費',
                    '車馬費',
                    '第四台'
                ])
            ) {
                return '管理服務費';
            } else {
                return '管理服務費(查無對應科目)';
            }
        }
    }

    // Create basic data structure for excel export module usage
    public function makeInvoiceMockData()
    {
        return [
            'invoice_count' => $this->invoice_count,
            'invoice_item_idx' => 1,
            'quantity' => 1,
            'tax_type' => 1,
            'tax_rate' => 0.05,
            'company_number' => '',
            'company_name' => '',
            'building_code' => '',
            'room_number' => '',
            'deposit_date' => '',
            'actual_deposit_date' => '',
            'invoice_collection_number' => '',
            'invoice_serial_number' => '',
            'invoice_price' => '',
            'comment' => '',
            'subtotal' => ''
        ];
    }

    public static function updateInvoiceNumber($receipts)
    {
        foreach ($receipts as $class => $receipt) {
            $class = array_search($class, __('general'));
            $model_name = studly_case(str_singular($class));
            foreach ($receipt as $receipt_key => $receipt_row) {
                $id = array_keys($receipt_row)[0];
                $model = app("App\\".$model_name)::find($id);
                // Check whether this model has generate receipt 
                if ($receipt_row[$id]['receipt_id'] == null) {
                    // Check whether invoice_serial_number exist to avoid generating redundant receipt
                    if( $receipt_row[$id]['invoice_serial_number'] == '' ){
                        continue;
                    }
                    $receipt = new Receipt();
                    $receipt->invoice_serial_number = $receipt_row[$id]['invoice_serial_number'];
                    $receipt->date = $receipt_row[$id]['invoice_date'];
                    $receipt->invoice_price = $receipt_row[$id]['invoice_price'];
                    $model->receipts()->save($receipt);
                }
                else{
                    $receipt = Receipt::find($receipt_row[$id]['receipt_id']);

                    // If the invoice_serial_number from user is different with receipt, notify invoice group users
                    if( $receipt->invoice_serial_number != $receipt_row[$id]['invoice_serial_number']){
                        NotificationService::notifyReceiptUpdated($model);
                    }
                    $receipt->invoice_serial_number = $receipt_row[$id]['invoice_serial_number'];
                    $receipt->date = $receipt_row[$id]['invoice_date'];
                    $receipt->invoice_price = $receipt_row[$id]['invoice_price'];
                    $receipt->save();
                }   
            }
        }
    }

    // If specific columns from user is different with receipt, notify invoice group users
    // Divided by model
    public static function compareReceipt($model, $data){
        if($model->receipts->isNotEmpty()){
            switch (class_basename($model)) {
                case 'ComanyIncome':
                    if($model['subject'] == '押金設算息' && $model['amount'] != $data['amount']){
                        NotificationService::notifyReceiptUpdated($model);
                    }
                    break;
                case 'PayLog':
                    if($model['amount'] != $data['amount'] || $model['paid_at'] != $data['paid_at']){
                        NotificationService::notifyReceiptUpdated($model);
                    }   
                    break;       
                case 'Maintenance':
                    if(($model['price'] != $data['price'] || $model['closed_date'] != $data['closed_date'])){
                        NotificationService::notifyReceiptUpdated($model);
                    }   
                    break;
                case 'TenantPayment':
                    if($model['amount'] != $data['amount'] || $model['due_time'] != $data['due_time'] || $model['subject'] != $data['subject']){
                        NotificationService::notifyReceiptUpdated($model);
                    } 
                    break;
                case 'TenantElectricityPayment':
                    if($model['amount'] != $data['amount']){
                        NotificationService::notifyReceiptUpdated($model);
                    }   
                    break;                          
                default:
                    break;
            }
        }
    }
}