<?php

namespace App\Services;

use App\Services\RoomService;
use App\Building;
use App\LandlordContract;
use App\MonthlyReport;
use Carbon\Carbon;

class MonthlyReportService
{

    /**
     * Get all data that monthly report needs.
     */
    public function getMonthlyReport(LandlordContract $landlordContract, $month, $year) {
        $data = [
            'meta'         => [],
            'rooms'        => [],
            'details'      => ['data'=>[], 'meta'=>[ 'total_incomes'=>0, 'total_expenses'=>0, 'total_landlord_other_subject_id'=>[]]],
            'payoffs'      => [],
            'shareholders' => [],
        ];
        
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
        $data['meta']['building_code'] = $landlordContract->building->rooms->pluck('room_code');
        $data['meta']['building_location'] = $landlordContract->building->location;
        $data['meta']['rooms_count'] = $landlordContract->building->rooms->count() - 1;
        $data['meta']['landlords_phones'] = $landlordContract->landlords->pluck('phones.*.value')->flatten();
        $data['meta']['account_numbers'] = $landlordContract->landlords->pluck('account_number');
        $data['meta']['account_address'] = array_merge($landlordContract->landlords->pluck('invoice_mailing_address')->toArray(), 
                                                        $landlordContract->landlords->pluck('faxNumbers.*.value')->flatten()->toArray());
        $data['meta']['rent_collection_time'] = $landlordContract->rent_collection_time;
        $data['meta']['agency_service_fee'] = $landlordContract->agency_service_fee;
        $data['meta']['total_management_fee'] = 0;
        $data['meta']['total_agency_fee'] = 0;
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
            $landlordOtherSubjects = $room->landlordOtherSubjects->whereBetween('expense_date', [$start_date, $end_date]);
            
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
                    'landlord_other_subject_id' => $landlordOtherSubject->id,
                ];
                array_push($data['details']['meta']['total_landlord_other_subject_id'], $landlordOtherSubject->id);
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
                                'paid_at' => $start_date,
                                'amount'  => $total_management_fee
                            ];
                            $roomData['meta']['room_total_expense'] += $total_management_fee;
                            $data['meta']['total_management_fee'] += $total_management_fee;;
                        } else if( $room->management_fee_mode === '固定' ) {
                            $management_fee = intval($room->management_fee);
                            $roomData['expenses'][] = [
                                'subject' => '管理服務費',
                                'paid_at' => $start_date,
                                'amount'  => $management_fee,
                            ];
                            $roomData['meta']['room_total_expense'] += $management_fee;
                            $data['meta']['total_management_fee'] += $management_fee;;
                        }
                        $firstRentPayment = $tenantContract->tenantPayments->where('subject', '租金')->sortBy('due_time')->first();
                        if ($payLog->loggable->id == $firstRentPayment->id) {
                            $agency_fee = intval(round($payLog->amount * $landlordContract->agency_service_fee));
                            $roomData['expenses'][] = [
                                'subject' => '仲介費',
                                'paid_at' => $payLog->paid_at,
                                'amount'  => $agency_fee,
                            ];
                            $data['meta']['total_agency_fee'] += $agency_fee;;
                            $roomData['meta']['room_total_expense'] += $agency_fee;
                        }                        
                        
                    }
                }         
            }
            $data['rooms'][] = $roomData;
            $data['meta']['total_income'] += $roomData['meta']['room_total_income'];
            $data['meta']['total_expense'] += $roomData['meta']['room_total_expense'];
        }
        // end section : rooms

        // section: add carry forward
        $carry_forward = MonthlyReport::where('year', $year)
                                        ->where('month', $month-1)
                                        ->where('landlord_contract_id', $landlordContract->id)
                                        ->get()->first()['carry_forward'] ?: 0;
        if( $carry_forward < 0 ){
            $detail_data = [
                'type'               => '',
                'room_code'          => '',
                'subject'            => '結轉上期',
                'bill_serial_number' => '',
                'bill_start_date'    => '',
                'bill_end_date'      => '',
                'paid_at'            => $end_date,
                'amount'             => $carry_forward,
            ];
            $data['details']['data'][] = $detail_data;
            $data['details']['meta']['total_expenses'] += -$carry_forward;
        }
        // end section: add carry forward

        // add detail total after room section
        $data['meta']['total_income'] += $data['details']['meta']['total_incomes'];
        $data['meta']['total_expense'] += $data['details']['meta']['total_expenses'];

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
            $data['meta']['total_income'] += $roomData['meta']['room_total_income'];
            $data['meta']['total_expense'] += $roomData['meta']['room_total_expense'];
        }
        // end section : payoffs


        // section : shareholders
        foreach ($landlordContract->building->shareholders as $shareholder) {
            $max_period = $shareholder->distribution_start_date->diffInMonths($shareholder->distribution_end_date)+1;
            $current_period = $shareholder->distribution_start_date->diffInMonths($start_date)+1;

            $distribution_fee = 0;
            if( $shareholder->distribution_method == '浮動' ){
                $total_revenue = $data['meta']['total_income'] - $data['meta']['total_expense'];
                if( $total_revenue > 0 ){
                    $distribution_fee = $total_revenue * $shareholder->investment_amount;
                }
            }
            else if ( $shareholder->distribution_method == '固定' ){
                $distribution_fee = $shareholder->investment_amount;
                $data['meta']['total_expense'] += $distribution_fee;
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
