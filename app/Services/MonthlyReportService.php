<?php

namespace App\Services;

use App\Services\RoomService;
use App\Building;
use App\LandlordContract;
use Carbon\Carbon;

class MonthlyReportService
{

    /**
     * Get all data that monthly report needs.
     */
    public function getMonthlyReport(LandlordContract $landlordContract, $month) {
        $data = [
            'meta'         => [],
            'rooms'        => [],
            'details'      => ['data'=>[], 'meta'=>[ 'total_incomes'=>0, 'total_expenses'=>0]],
            'payoffs'      => [],
            'shareholders' => [],
        ];
        
        // Set query date
        $month - Carbon::now()->month > 1 ? $year = Carbon::now()->year-1 : $year=Carbon::now()->year;
        $start_date = Carbon::create($year, $month);
        $end_date = $start_date->copy()->endOfMonth();


        // TODO: loading these relations are probably better using repository

        $landlordContract->loadMissing('building.rooms');

        $landlordContract->loadMissing('landlords');
        $landlordContract->landlords->loadMissing(['phones', 'faxNumbers']);

        $landlordContract->loadMissing('building.shareholders');

        /*****  packing data  *****/
        // since landlord -- landlord contract become n to n,
        // and landlord -- contact info are 1 to n
        // you should pack your own logic of how to display them

        // section : meta
        $data['meta']['landlord_name'] = $landlordContract->landlords->pluck('name');
        // period calculation
        $relative_landlordContracts = $landlordContract->landlords->first()->landlordContracts->where('commission_end_date', '>', $start_date);
        $period_start = $relative_landlordContracts->first()->commission_start_date->format('Y-m-d');
        $period_end = $relative_landlordContracts->last()->commission_end_date->format('Y-m-d');

        $data['meta']['period'] = $period_start.' ~ '.$period_end;
        $data['meta']['building_name'] = $landlordContract->building->name;
        $data['meta']['building_address'] = $landlordContract->building->address;
        $data['meta']['rooms_count'] = $landlordContract->building->rooms->count() - 1;
        $data['meta']['landlords_phones'] = $landlordContract->landlords->pluck('phones.*.value')->flatten();
        $data['meta']['account_numbers'] = $landlordContract->landlords->pluck('account_number');
        $data['meta']['invoice_mailing_addresses'] = $landlordContract->landlords->pluck('invoice_mailing_address');
        $data['meta']['fax_numbers'] = $landlordContract->landlords->pluck('faxNumbers.*.value')->flatten();
        $data['meta']['agency_service_fee'] = $landlordContract->agency_service_fee;
        $data['meta']['total_income'] = 0;
        $data['meta']['total_expense'] = 0;
        // TODO: calculation of incomes and expenses
        // end section : meta

        // section : rooms
        foreach ($landlordContract->building->rooms as $room) {
            $roomData = [
                'meta' => [
                    'room_total_income' => 0,
                    'room_total_expense' => 0,
                ],
                'incomes' => [
                    // [  // example
                    //     'subject' => '租金',
                    //     'month'   => '8月',
                    //     'paid_at' => '2019-08-20',
                    //     'amount'  => '1000',
                    // ]
                ],
                'expenses' => []
            ];

            // section : details
            $landlordPayments = $room->landlordPayments->whereBetween('collection_date', [$start_date, $end_date]);
            $landlordOtherSubjects = $room->landlordPayments->whereBetween('expense_date', [$start_date, $end_date]);
            
            foreach ($landlordPayments as $landlordPayment) {
                $data['details']['data'][] = [
                    'type'               => 'expense',
                    'room_code'          => $room->room_code,
                    'subject'            => $landlordPayment->subject,
                    'bill_serial_number' => $landlordPayment->bill_serial_number,
                    'bill_start_date'    => $landlordPayment->bill_start_date,
                    'bill_end_date'      => $landlordPayment->bill_end_date,
                    'paid_at'            => $landlordPayment->collection_date,
                    'amount'             => $landlordPayment->amount,
                ];
                $data['details']['meta']['total_expenses'] += $landlordPayment->amount;
            }

            foreach ($landlordOtherSubjects as $landlordOtherSubject) {
                $data['details']['data'][] = [
                    'type'               => $landlordOtherSubject->income_or_expense === '收入' ? 'income' : 'expense',
                    'room_code'          => $room->room_code,
                    'subject'            => $landlordOtherSubject->subject,
                    'bill_serial_number' => '',
                    'bill_start_date'    => '',
                    'bill_end_date'      => '',
                    'paid_at'            => $landlordOtherSubject->expense_date,
                    'amount'             => $landlordOtherSubject->amount,
                ];
                if($landlordOtherSubject->income_or_expense === '收入'){
                    $data['details']['meta']['total_incomes'] += $landlordOtherSubject->amount;
                }
                else{
                    $data['details']['meta']['total_expenses'] += $landlordOtherSubject->amount;
                }
            }
            // end section : details
            if ($room->room_code === '公用') {
                continue;
            }

            $roomData['meta']['room_number'] = $room->room_number;
            $roomData['meta']['management_fee'] = $room->management_fee;
            $roomData['meta']['management_fee_mode'] = $room->management_fee_mode;

            $tenantContract = $room->activeContracts->first();
            if (is_null($tenantContract)) {
                // there are no active contracts
            } else {
                $payLogs = $tenantContract->payLogs->whereBetween('paid_at', [$start_date, $end_date]);
                foreach ($payLogs as $payLog) {
                    $roomData['incomes'][] = [
                        'subject' => $payLog->subject,
                        'month'   => Carbon::parse($payLog->loggable->due_time)->month . '月',
                        'paid_at' => $payLog->paid_at,
                        'amount'  => $payLog->amount,
                    ];
                    $roomData['meta']['room_total_income'] += $payLog->amount;

                    // pack expense data relative '租金'
                    if($payLog->subject == '租金'){
                        if ($room->management_fee_mode === '比例') {
                            $total_management_fee = 0;
                            $total_management_fee += intval(round($payLog->amount * $room->management_fee / 100));
                            $roomData['expenses'][] = [
                                'subject' => '管理服務費',
                                'paid_at' => $payLog->paid_at,
                                'amount'  => $payLog->amount,
                            ];
            
                            $roomData['meta']['room_total_expense'] += $total_management_fee;
                        } else if( $room->management_fee_mode === '固定' ) {
                            $management_fee = intval($room->management_fee);
                            $roomData['expenses'][] = [
                                'subject' => '管理服務費',
                                'paid_at' => $start_date,
                                'amount'  => $management_fee,
                            ];
                            $roomData['meta']['room_total_expense'] += $management_fee;
                        }
                        $firstRentPayment = $tenantContract->tenantPayments->where('subject', '租金')->sortBy('due_time')->first();
                        if ($payLog->loggable->id == $firstRentPayment->id) {
                            $agency_fee = intval(round($payLog->amount * $landlordContract->taxable_charter_fee));
                            $roomData['expenses'][] = [
                                'subject' => '仲介費',
                                'paid_at' => $payLog->paid_at,
                                'amount'  => $agency_fee,
                            ];
            
                            $roomData['meta']['room_total_expense'] += $total_management_fee;
                        }                        
                        
                    }
                }         
            }
            $data['rooms'][] = $roomData;
        }
        // end section : rooms

        // section : payoffs
        foreach ($landlordContract->building->rooms as $room) {
            $tenantContract = $room->activeContracts->first();
            if (is_null($tenantContract)) {
                // there are no active contracts
            }
            else{
                $payoffPayments = $tenantContract->tenantPayments
                                                ->where('is_pay_off', true)
                                                ->whereBetween('due_time', [$start_date, $end_date]);
                if( $payoffPayments->count() > 0 ){
                    $roomData = [
                        'meta' => [
                            'room_total_income' => 0,
                            'room_total_expense' => 0,
                        ],
                        'incomes' => [],
                        'expenses' => []
                    ];
                    $roomData['meta']['room_number'] = $room->room_number;
                    foreach( $payoffPayments as $payoffPayment ){
                        if( $payoffPayment->amount <= 0 ){
                            $roomData['incomes'][] = [
                                'subject' => $payoffPayment->subject,
                                'month'   => Carbon::parse($payoffPayment->due_time)->month . '月',
                                'paid_at' => $payoffPayment->due_time,
                                'amount'  => -$payoffPayment->amount,
                            ];  
                            $roomData['meta']['room_total_income'] += -$payoffPayment->amount;        
                        }
                        else{
                            $roomData['expenses'][] = [
                                'subject' => $payoffPayment->subject,
                                'month'   => Carbon::parse($payoffPayment->due_time)->month . '月',
                                'paid_at' => $payoffPayment->due_time,
                                'amount'  => $payoffPayment->amount,
                            ];
                            $roomData['meta']['room_total_expense'] += $payoffPayment->amount;                   
                        }
                    }
                    $data['payoffs'][] = $roomData;
                }
            }
        }
        // end section : payoffs


        // section : shareholders
        foreach ($landlordContract->building->shareholders as $shareholder) {
            $max_period = $shareholder->distribution_start_date->diffInMonths($shareholder->distribution_end_date)+1;
            $current_period = $shareholder->distribution_start_date->diffInMonths($start_date)+1;

            $distribution_fee = 0;
            if( $shareholder->distribution_method == '浮動' ){
                $distribution_fee = $data['meta']['total_income'] * $shareholder->investment_amount;
            }
            else if ( $shareholder->distribution_method == '固定' ){
                $distribution_fee = $shareholder->investment_amount;
            }

            $data['shareholders'][] = [
                'name' => $shareholder->name,
                'current_period' => $current_period,
                'max_period' => $max_period,
                'distribution_fee' => $distribution_fee
            ];
        }
        // end section : shareholders
        return collect($data);
    }

}
