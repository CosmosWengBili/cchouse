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
    public function getMonthlyReport(LandlordContract $landlordContract) {
        $data = [
            'meta'         => [],
            'rooms'        => [],
            'details'      => [],
            'shareholders' => [],
        ];

        // TODO: loading these relations are probably better using repository

        $landlordContract->loadMissing('building.rooms');
        // load rooms' paylogs whose tenant payment has is_pay_off == false through tenantContracts.tenantPayments
        $landlordContract->building->rooms->load([
            'activeContracts.tenantPayments' => function($query) {
                $query->where('is_pay_off', false)->with('payLogs');
            }
        ]);

        $landlordContract->loadMissing('landlords');
        $landlordContract->landlords->loadMissing(['phones', 'faxNumbers']);

        $landlordContract->loadMissing('building.shareholders');

        /*****  packing data  *****/
        // since landlord -- landlord contract become n to n,
        // and landlord -- contact info are 1 to n
        // you should pack your own logic of how to display them

        // section : meta
        $data['meta']['landlord_name'] = $landlordContract->landlords->pluck('name');
        // TODO: period calculation
        $data['meta']['period'] = 'TODO';
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

            if ($room->room_code === '公用') {
                // section : details

                foreach ($room->landlordPayments as $landlordPayment) {
                    $data['details'][] = [
                        'type'               => 'expense',
                        'subject'            => $landlordPayment->subject,
                        'bill_serial_number' => $landlordPayment->bill_serial_number,
                        'bill_start_date'    => $landlordPayment->bill_start_date,
                        'bill_end_date'      => $landlordPayment->bill_end_date,
                        'paid_at'            => $landlordPayment->collection_date,
                        'amount'             => $landlordPayment->amount,
                    ];
                }

                foreach ($room->landlordOtherSubjects as $landlordOtherSubject) {
                    $data['details'][] = [
                        'type'               => $landlordOtherSubject->income_or_expense === '收入' ? 'income' : 'expense',
                        'subject'            => $landlordOtherSubject->subject,
                        'bill_serial_number' => '',
                        'bill_start_date'    => '',
                        'bill_end_date'      => '',
                        'paid_at'            => $landlordOtherSubject->expense_date,
                        'amount'             => $landlordOtherSubject->amount,
                    ];
                }
                // end section : details

                continue;
            }

            $roomData['meta']['room_number'] = $room->room_number;
            $roomData['meta']['management_fee'] = $room->management_fee;
            $roomData['meta']['management_fee_mode'] = $room->management_fee_mode;

            $tenantContract = $room->activeContracts->first();
            if (is_null($tenantContract)) {
                // there are no active contracts
            } else {
                foreach ($tenantContract->tenantPayments as $tenantPayment) {
                    foreach ($tenantPayment->payLogs as $payLog) {
                        $roomData['incomes'][] = [
                            'subject' => $payLog->subject,
                            'month'   => Carbon::parse($tenantPayment->due_time)->month . '月',
                            'paid_at' => $payLog->paid_at,
                            'amount'  => $payLog->amount,
                        ];
    
                        $roomData['meta']['room_total_income'] += $payLog->amount;
                    }
                }

                if ($room->management_fee_mode === '比例') {
                    $rentPayLogs = $tenantContract->tenantPayments->where('subject', '租金')->pluck('payLogs')->flatten();
                    $total_management_fee = 0;
    
                    foreach ($rentPayLogs as $payLog) {
                        $total_management_fee += intval(round($payLog->amount * $room->management_fee / 100));
                        $roomData['expenses'][] = [
                            'subject' => '管理服務費',
                            'paid_at' => $payLog->paid_at,
                            'amount'  => $payLog->amount,
                        ];
                    }
    
                    $roomData['meta']['room_total_expense'] += $total_management_fee;
                } else {
                    // TODO: date of fixed management fee income
                    $management_fee = intval($room->management_fee);
                    $roomData['expenses'][] = [
                        'subject' => '管理服務費',
                        'paid_at' => 'TODO',
                        'amount'  => $management_fee,
                    ];
                    $roomData['meta']['room_total_expense'] += $management_fee;
                }
            }

            // TODO: pack other expenses

            $data['rooms'][] = $roomData;
        }
        // end section : rooms

        // section : shareholders
        foreach ($landlordContract->building->shareholders as $shareholder) {
            $data['shareholders'][] = [
                'name' => $shareholder->name,
                // TODO: pack other fields
            ];
        }
        // end section : shareholders
        return collect($data);
    }
}
