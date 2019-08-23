<?php

namespace App\Services;

use Carbon\Carbon;
use App\PayLog;
use App\TenantPayment;
use App\TenantContract;
use App\TenantElectricityPayment;
use App\LandlordContract;
use App\LandlordPayment;
use App\Deposit;
use App\SystemVariable;
use App\Receipt;

class ReceiptService
{
    public static function makeInvoiceData($start_date, $end_date)
    {
        // Init pay logs data
        $pay_logs = PayLog::whereBetween('paid_at', [$start_date, $end_date])
            ->where('receipt_type', '=', '發票')
            ->orderBy('virtual_account', 'DESC')
            ->orderBy('paid_at', 'ASC')
            ->with([
                'loggable.tenantContract.tenant',
                'loggable.tenantContract.room.building.landlordContracts'
            ])
            ->get();

        // Init the variables which would be used inside invoice data for loop
        $invoice_count = 0;
        $invoice_item_count = 0;
        $payment_count = 0;
        $subtotal = 0;
        $comment = '';
        $current_tenant_contract_id = 0;
        $invoice_data = array();

        // Init the variables which would be used to detect whether over landlord rent price
        $building_price_map = array();

        // Generate invoice data
        foreach ($pay_logs as $pay_log_key => $pay_log) {
            // Check whether the payments could be add to invoice data
            if (
                $pay_log['loggable']['collected_by'] != '公司' &&
                $pay_log['loggable']['subject'] != '電費'
            ) {
                continue;
            }

            $data = self::makeInvoiceMockData();

            $invoice_item_count++;
            $pay_log_tenant_contract_id =
                $pay_log['loggable']['tenantContract']->id;

            // Update subtotal-use index
            if ($pay_log_tenant_contract_id != $current_tenant_contract_id) {
                $invoice_count++;
                $invoice_item_count = 1;
                $current_tenant_contract_id = $pay_log_tenant_contract_id;
                $payment_count = 0;

                $tenantContract = $pay_log->loggable->tenantContract;

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
                        ->get();
                    foreach ($tmp_payments as $payment_key => $payment) {
                        $payment_count += $payment->payLogs
                            ->whereBetween('paid_at', [$start_date, $end_date])
                            ->where('receipt_type', '=', '發票')
                            ->count();
                    }
                }
            }

            // Set normal value
            $data['invoice_count'] = $invoice_count;
            $data['invoice_date'] = $pay_log->paid_at->format('Y-m-d');
            $data['invoice_item_idx'] = $invoice_item_count;
            $data['invoice_item_name'] = self::makeInvoiceItemName(
                $pay_log['loggable'],
                'payment'
            );
            $data['quantity'] = 1;
            $data['amount'] = $pay_log->amount;
            $data['tax_type'] = 1;
            $data['tax_rate'] = 0.05;

            // Set value for maping relative payment model
            $data['data_table'] = $pay_log->loggable->getTable();
            $data['data_table_id'] = $pay_log->loggable->id;
            $data['data_receipt_id'] = $pay_log->loggable->receipts->first()['id'];

            $subtotal += $pay_log->loggable->amount;
            $comment =
                $comment .
                ($pay_log->loggable->comment .
                    ';' .
                    $pay_log->loggable->tenantContract->room->comment);

            if ($payment_count == $invoice_item_count) {
                $data['invoice_item_idx'] = $data['invoice_item_idx'] + 1;
                if (
                    $pay_log->loggable->tenantContract->tenant->is_legal_person
                ) {
                    $data['company_number'] =
                        $pay_log->loggable->tenantContract->tenant->certificate_number;
                    $data['company_name'] =
                        $pay_log->loggable->tenantContract->tenant->name;
                }
                $data['comment'] = $comment;
                $data['room_code'] =
                    $pay_log->loggable->tenantContract->room->room_code;
                $data['room_number'] =
                    $pay_log->loggable->tenantContract->room->room_number;
                $data['deposit_date'] = $pay_log->paid_at->format('Y-m-d');
                $data['actual_deposit_date'] = $pay_log->paid_at->format(
                    'Y-m-d'
                );
                $data['invoice_collection_number'] =
                    $pay_log->loggable->tenantContract->invoice_collection_number;
                $data['invoice_serial_number'] = $pay_log->loggable->receipts->first()['invoice_serial_number'];
                $data['invoice_price'] = round($subtotal * $data['tax_rate']);
                $data['subtotal'] = $subtotal;
                $subtotal = 0;
            }

            array_push($invoice_data, $data);
        }

        // Generate deposit interest, collection data
        $deposit_interest_data = self::makeDepositInterest(
            $start_date,
            $end_date
        );
        $maintenance_data = self::makeMaintenance($start_date, $end_date);
        $deposit_data = self::makeDeposits($start_date, $end_date);

        $invoice_data = array_merge(
            $invoice_data,
            $deposit_interest_data,
            $maintenance_data,
            $deposit_data
        );
        return $invoice_data;
    }

    public static function makeReceiptData($start_date, $end_date)
    {
        $receipt_data = array();
        $landlord_contract_ids = array();

        // Init pay logs data
        $pay_logs = PayLog::whereBetween('paid_at', [$start_date, $end_date])
                    ->where('receipt_type', '=', '收據')
                    ->with([
                        'loggable.tenantContract.tenant',
                        'loggable.tenantContract.room.building.landlordContracts'
                    ])
                    ->get();

        foreach($pay_logs as $log_key => $pay_log){
            $current_landlord_contract = $pay_log->loggable->tenantContract->room->building->activeContracts->first();
            array_push($landlord_contract_ids, $current_landlord_contract->id);

            $landlord_contract_key = $current_landlord_contract->id;
            if (
                isset(
                    $landlord_contract_receipt_map[
                        $landlord_contract_key
                    ]
                )
            ) {
                $landlord_contract_receipt_map[
                    $landlord_contract_key
                ] += $pay_log->amount;
            } else {
                $landlord_contract_receipt_map[$landlord_contract_key] =
                    $pay_log->amount;
            }
        }
        

        $landlord_contract_ids = array_unique($landlord_contract_ids);
        $landlord_contracts = LandlordContract::whereIn(
            'id',
            $landlord_contract_ids
        )
            ->with(['landlords', 'building.rooms'])
            ->get();
        foreach ($landlord_contracts as $contract_key => $landlord_contract) {
            $data = array();
            $data['room_code'] = implode(
                ',',
                $landlord_contract->building->rooms
                    ->pluck('room_code')
                    ->toArray()
            );
            $data['group'] = 'A';
            $data['city'] = $landlord_contract->building->city;
            $data['district'] = $landlord_contract->building->district;
            $data['address'] = $landlord_contract->building->address;
            $data['tax_number'] = $landlord_contract->building->tax_number;
            $data['landlord_name'] = implode(
                ',',
                $landlord_contract->landlords->pluck('name')->toArray()
            );
            $data['taxable_charter_fee'] =
                $landlord_contract->taxable_charter_fee;
            $data['actual_charter_fee'] =
                $landlord_contract_receipt_map[$landlord_contract->id];
            $data['rent_collection_time'] =
                $landlord_contract->rent_collection_time;
            $data['rent_collection_year'] = Carbon::now()->year;
            $data['commission_end_date'] =
                $landlord_contract->commission_end_date;

            array_push($receipt_data, $data);
        }

        return $receipt_data;
    }

    public static function makeDepositInterest($start_date, $end_date)
    {
        // Set time cauculate used variables
        $start_year = $start_date->year;
        $end_year = $end_date->year;
        $start_idx = $start_date->month;
        $end_idx =
            $end_date == $end_date->copy()->endOfMonth()
                ? $end_date->month
                : $end_date->subMonth()->month;

        $end_idx = $end_idx - $start_idx + 12 * ($end_year - $start_year);
        $current_date = $start_date->endOfMonth();
        $date_data = array();

        // Find each deposit interest point during period
        for ($month_idx = 0; $month_idx <= $end_idx; $month_idx++) {
            array_push($date_data, $current_date);
            $current_date = $current_date
                ->copy()
                ->addMonthsWithoutOverflow(1)
                ->endOfMonth();
        }

        // Genenate deposit interest data
        $invoiceData = array();
        foreach ($date_data as $date_key => $date) {
            $tenant_contracts = TenantContract::where([
                ['contract_end', '>', $date],
                ['contract_start', '<', $date],
                ['deposit_paid', '>', 0]
            ])
                ->with(['tenant', 'room'])
                ->get();

            foreach ($tenant_contracts as $contract_key => $tenant_contract) {
                $data = self::makeInvoiceMockData();
                $data['invoice_date'] = $date->format('Y-m-d');
                $data['invoice_item_name'] = '押金設算息';
                $deposit_interest = SystemVariable::where(
                    'code',
                    '=',
                    '押金設算息'
                )->first()->value;
                $data['amount'] = round(
                    $tenant_contract->deposit_paid * $deposit_interest
                );
                $data['data_table'] = $tenant_contract->getTable();
                $data['data_table_id'] = $tenant_contract->id;
                $data['data_receipt_id'] = $tenant_contract->receipts->where('date', $date->format('Y-m-d').' 00:00:00')->first()['id'];

                if ($tenant_contract->tenant->is_legal_person) {
                    $data['company_number'] =
                        $tenant_contract->tenant->certificate_number;
                    $data['company_name'] = $tenant_contract->tenant->name;
                }
                $data['comment'] = '';
                $data['room_code'] = $tenant_contract->room->room_code;
                $data['room_number'] = $tenant_contract->room->room_number;
                $data['deposit_date'] = $date->format('Y-m-d');
                $data['actual_deposit_date'] = $date->format('Y-m-d');
                $data['invoice_collection_number'] =
                    $tenant_contract->invoice_collection_number;
                $data['invoice_price'] = round(
                    $data['amount'] * $data['tax_rate']
                );
                $data['invoice_serial_number'] = $tenant_contract->receipts->where('date', $date->format('Y-m-d').' 00:00:00')->first()['invoice_serial_number'];
                array_push($invoiceData, $data);
            }
        }
        return $invoiceData;
    }

    public static function makeCollection($start_date, $end_date)
    {
        $collections = DebtCollection::where([
            'is_penalty_collected',
            '=',
            true
        ])
            ->whereBetween(['received_at', [$start_date, $end_date]])
            ->with(['tenantContract.tenant', 'tenantContract.room']);

        $collection_data = array();
        foreach ($collections as $collection_key => $collection) {
            $data = self::makeInvoiceMockData();
            $data['invoice_date'] = $collection->received_at->format('Y-m-d');
            $data['invoice_item_name'] = '行政手續費';
            $data['amount'] = 300;

            // Set value for maping relative payment model
            $data['data_table'] = $collection->getTable();
            $data['data_table_id'] = $collection->id;
            $data['data_receipt_id'] = $collection->receipts->first()['id'];

            if ($collection->tenant_contract->tenant->is_legal_person) {
                $data['company_number'] =
                    $tenant_contract->tenant->certificate_number;
                $data['company_name'] = $tenant_contract->tenant->name;
            }
            $data['comment'] = '';
            $data['room_code'] = $tenant_contract->room->room_code;
            $data['room_number'] = $tenant_contract->room->room_number;
            $data['deposit_date'] = $collection->received_at->format('Y-m-d');
            $data['actual_deposit_date'] = $collection->received_at->format(
                'Y-m-d'
            );
            $data['invoice_collection_number'] =
                $tenant_contract->invoice_collection_number;
            $data['invoice_price'] = round($data['amount'] * $data['tax_rate']);
            $data['invoice_serial_number'] = $collection->receipts->first()['invoice_serial_number'];

            array_push($collection_data, $data);
        }

        return $collection_data;
    }

    public static function makeMaintenance($start_date, $end_date)
    {
        $landlord_payments = LandlordPayment::where(
            'subject',
            'like',
            '維修案件'
        )
            ->whereBetween('collection_date', [$start_date, $end_date])
            ->with(['room.building.landlordContracts'])
            ->get();
        $maintenance_data = array();
        foreach ($landlord_payments as $payment_key => $landlord_payment) {
            $landlords = $landlord_payment->room->building->landlordContracts->last()
                ->landlords;
            foreach ($landlords as $landlord_key => $landlord) {
                $data = self::makeInvoiceMockData();
                $data['invoice_date'] = $landlord_payment->created_at->format(
                    'Y-m-d'
                );
                $data['invoice_item_name'] = '管理服務費(維修費)';
                $data['amount'] = $landlord_payment->amount;

                // Set value for maping relative payment model
                $data['data_table'] = $landlord_payment->getTable();
                $data['data_table_id'] = $landlord_payment->id;
                $data['data_receipt_id'] = $landlord_payment->receipts->first()['id'];

                if ($landlord->is_legal_person) {
                    $data['company_number'] = $landlord->certificate_number;
                    $data['company_name'] = $landlord->name;
                }
                $data['comment'] = '';
                $data['room_code'] = $landlord_payment->room->room_code;
                $data['room_number'] = $landlord_payment->room->room_number;
                $data['deposit_date'] = $landlord_payment->created_at->format(
                    'Y-m-d'
                );
                $data[
                    'actual_deposit_date'
                ] = $landlord_payment->created_at->format('Y-m-d');
                $data['invoice_collection_number'] =
                    $landlord->invoice_collection_number;
                $data['invoice_price'] = round(
                    $data['amount'] * $data['tax_rate']
                );
                $data['invoice_serial_number'] = $landlord_payment->receipts->first()['invoice_serial_number'];

                array_push($maintenance_data, $data);
            }
        }
        return $maintenance_data;
    }
    public static function makeDeposits($start_date, $end_date)
    {
        $deposits = Deposit::where('deposit_confiscated_amount', '>', 1)
            ->whereBetween('confiscated_or_returned_date', [
                $start_date,
                $end_date
            ])
            ->with(['tenantContract.room.building.landlordContracts'])
            ->get();

        $deposit_data = array();
        foreach ($deposits as $deposit_key => $deposit) {
            $landlords = $deposit->tenantContract->room->building->landlordContracts->last()
                ->landlords;
            foreach ($landlords as $landlord_key => $landlord) {
                $data = self::makeInvoiceMockData();
                $data[
                    'invoice_date'
                ] = $deposit->confiscated_or_returned_date->format('Y-m-d');
                $data['invoice_item_name'] = '管理服務費';
                $data['amount'] = $deposit->deposit_confiscated_amount;

                // Set value for maping relative payment model
                $data['data_table'] = $deposit->getTable();
                $data['data_table_id'] = $deposit->id;
                $data['data_receipt_id'] = $deposit->receipts->first()['id'];

                if ($landlord->is_legal_person) {
                    $data['company_number'] = $landlord->certificate_number;
                    $data['company_name'] = $landlord->name;
                }
                $data['room_code'] = $deposit->tenantContract->room->room_code;
                $data['room_number'] =
                    $deposit->tenantContract->room->room_number;
                $data[
                    'deposit_date'
                ] = $deposit->confiscated_or_returned_date->format('Y-m-d');
                $data[
                    'actual_deposit_date'
                ] = $deposit->confiscated_or_returned_date->format('Y-m-d');
                $data['invoice_collection_number'] =
                    $landlord->invoice_collection_number;
                $data['invoice_price'] = round(
                    $data['amount'] * $data['tax_rate']
                );
                $data['invoice_serial_number'] = $deposit->receipts->first()['invoice_serial_number'];

                array_push($deposit_data, $data);
            }
        }

        return $deposit_data;
    }

    public static function makeInvoiceItemName($object, $type)
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
            } elseif (
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
    public static function makeInvoiceMockData()
    {
        return [
            'invoice_count' => 1,
            'invoice_item_idx' => 1,
            'quantity' => 1,
            'tax_type' => 1,
            'tax_rate' => 0.05,
            'company_number' => '',
            'company_name' => '',
            'room_code' => '',
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
            $model_name = studly_case(str_singular($class));
            foreach ($receipt as $receipt_key => $receipt_row) {
                $id = array_keys($receipt_row)[0];
                $model = app("App\\".$model_name)::find($id);
                if ($receipt_row[$id]['receipt_id'] == null) {
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
    public static function compareReceipt($model, $data){
        if($model->receipts->isNotEmpty()){
            switch (class_basename($model)) {
                case 'TenantContract':
                    if($model['deposit_paid'] != $data['deposit_paid']){
                        NotificationService::notifyReceiptUpdated($model);
                    }
                    break;
                case 'DebtCollection':
                    if($model['is_penalty_collected'] != $data['is_penalty_collected']){
                        NotificationService::notifyReceiptUpdated($model);
                    }  
                    break; 
                case 'Deposit':
                    if($model['deposit_confiscated_amount'] != $data['deposit_confiscated_amount'] || $model['confiscated_or_returned_date'] != $data['confiscated_or_returned_date']){
                        NotificationService::notifyReceiptUpdated($model);
                    }   
                    break;       
                case 'LandlordPayment':
                    if(!strpos($model['subject'], "維修案件") && ($model['amount'] != $data['amount'] || $model['collection_date'] != $data['collection_date'])){
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
