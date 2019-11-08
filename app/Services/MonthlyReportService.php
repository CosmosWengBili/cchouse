<?php

namespace App\Services;

use App\Services\RoomService;
use App\Building;
use App\LandlordContract;
use App\TenantElectricityPayment;
use App\MonthlyReport;
use Carbon\Carbon;

class MonthlyReportService
{
    /**
     * Get all data that monthly report needs.
     * @param LandlordContract $landlordContract
     * @param                  $month
     * @param                  $year
     *
     * @return \Illuminate\Support\Collection
     */
    public function getMonthlyReport(LandlordContract $landlordContract, $month, $year)
    {
        $data = [
            'meta' => [],
            'rooms' => [],
            'details' => ['data' => [], 'meta' => ['total_incomes' => 0, 'total_expenses' => 0, 'total_landlord_other_subject_id' => []]],
            'payoffs' => [],
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
        $shareholders = $landlordContract->building->shareholders;
        $has_shareholder = $shareholders->count() > 0;

        // section : meta
        // period calculation
        $relative_landlordContracts = $landlordContract->landlords->first()->landlordContracts->where('commission_end_date', '>', $start_date);
        $period_start = $relative_landlordContracts->first()->commission_start_date;
        $period_end = $relative_landlordContracts->last()->commission_end_date;

        $data['meta']['period'] = $period_start.' ~ '.$period_end;
        $data['meta']['building_code'] = $landlordContract->building->building_code;
        $data['meta']['building_location'] = $landlordContract->building->location;
        $data['meta']['rooms_count'] = $landlordContract->building->rooms->count() - 1;

        if ($has_shareholder && $landlordContract->commission_type == '包租') {
            $data['meta']['landlord_name'] = collect(['依股東']);
            $data['meta']['landlords_phones'] = collect(['依股東']);
            $data['meta']['account_numbers'] = ['依股東'];
            $data['meta']['account_address'] = ['依股東'];
        } else {
            $data['meta']['landlord_name'] = $landlordContract->landlords->pluck('name');
            $data['meta']['landlords_phones'] = $landlordContract->landlords->pluck('phones.*.value')->flatten();
            $account_numbers = [];
            foreach ($landlordContract->landlords as $landlord) {
                $account_numbers[] = $landlord->bank_code.' '.$landlord->branch_code.' '.$landlord->account_name.' '.$landlord->account_number;
            }
            $data['meta']['account_numbers'] = $account_numbers;
            $data['meta']['account_address'] = array_merge(
                $landlordContract->landlords->pluck('invoice_mailing_address')->toArray(),
                $landlordContract->landlords->pluck('faxNumbers.*.value')->flatten()->toArray()
            );
        }
        $data['meta']['rent_collection_time'] = $landlordContract->rent_collection_time;
        $data['meta']['agency_service_fee'] = $landlordContract->agency_service_fee;
        $data['meta']['total_management_fee'] = 0;
        $data['meta']['total_agency_fee'] = 0;
        $data['meta']['total_income'] = 0;
        $data['meta']['total_expense'] = 0;
        // end section : meta

        // section : add charter_fee for charter contract

        if ($landlordContract->commission_type == '包租') {
            $data['details']['data'][] = [
                'type' => 'expense',
                'subject' => '租金票',
                'bill_serial_number' => '',
                'bill_start_date' => '',
                'bill_end_date' => '',
                'paid_at' => $end_date,
                'amount' => $landlordContract->charter_fee,
            ];
            $data['details']['meta']['total_expenses'] += $landlordContract->charter_fee;
        }
        //

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
            $landlordPayments = $room->landlordPayments
                                    ->whereBetween('collection_date', [$start_date, $end_date])
                                    ->where('subject', 'like', '維修案件%')
                                    // ->where('billing_vendor', 'CCHOUSE')
                                    ;

            $landlordOtherSubjects = $room->landlordOtherSubjects
                                        ->where('subject_type', '!=', '點交')
                                        ->whereBetween('expense_date', [$start_date, $end_date]);

            foreach ($landlordPayments as $landlordPayment) {
                $data['details']['data'][] = [
                    'type' => 'expense',
                    'room_code' => $room->room_code,
                    'subject' => $landlordPayment->subject,
                    'bill_serial_number' => $landlordPayment->bill_serial_number,
                    'bill_start_date' => $landlordPayment->bill_start_date,
                    'bill_end_date' => $landlordPayment->bill_end_date,
                    'paid_at' => $landlordPayment->collection_date,
                    'amount' => $landlordPayment->amount,
                ];
                $data['details']['meta']['total_expenses'] += $landlordPayment->amount;
            }

            foreach ($landlordOtherSubjects as $landlordOtherSubject) {
                $data['details']['data'][] = [
                    'type' => $landlordOtherSubject->income_or_expense === '收入' ? '收入' : '支出',
                    'room_code' => $room->room_code,
                    'subject' => $landlordOtherSubject->subject,
                    'bill_serial_number' => '',
                    'bill_start_date' => '',
                    'bill_end_date' => '',
                    'paid_at' => $landlordOtherSubject->expense_date,
                    'amount' => $landlordOtherSubject->amount,
                    'landlord_other_subject_id' => $landlordOtherSubject->id,
                ];
                array_push($data['details']['meta']['total_landlord_other_subject_id'], $landlordOtherSubject->id);
                if ($landlordOtherSubject->income_or_expense === '收入') {
                    $data['details']['meta']['total_incomes'] += $landlordOtherSubject->amount;
                } else {
                    $data['details']['meta']['total_expenses'] += $landlordOtherSubject->amount;
                }
            }

            //
            $maintenances = $room->maintenances()
                                ->where('afford_by', '=', '房東')
                                ->where('status', '=', '案件完成')
                                ->whereBetween('updated_at', [$start_date, $end_date])
                                ->get();

            foreach ($maintenances as $maintenance) {
                $data['details']['data'][] = [
                    'type' => '支出',
                    'room_code' => $room->room_code,
                    'subject' => $room->room_code.' '.$maintenance->work_type.' '.$maintenance->incident_details,
                    'bill_serial_number' => '',
                    'bill_start_date' => '',
                    'bill_end_date' => '',
                    'paid_at' => $maintenance->updated_at,
                    'amount' => $maintenance->price
                ];

                $data['details']['meta']['total_expenses'] += $maintenance->price;
            }

            $tenantContracts = $room->tenantContracts
                ->where('contract_start', '<', $end_date)
                ->where('contract_end', '>', $start_date);

            // end section : details
            if ($room->room_layout === '公用') {
                continue;
            }

            $roomData['meta']['room_number'] = $room->room_number;
            $roomData['meta']['management_fee'] = $room->management_fee;
            $roomData['meta']['management_fee_mode'] = $room->management_fee_mode;
            $roomData['meta']['status'] = $room->room_status;

            if ($tenantContracts->count() > 0) {
                foreach ($tenantContracts as $tenantContract) {
                    $payLogs = $tenantContract->payLogs->whereBetween('paid_at', [$start_date, $end_date])
                                                        ->whereIn('loggable_type', ['App\TenantPayment', 'App\TenantElectricityPayment']);

                    // PayLog 裡面好像沒有 collected_by Attribute
                    $payLogs->reject(function ($payLog) {
                        return $payLog->collected_by == '公司';
                    });

                    foreach ($payLogs as $payLog) {
                        $roomData['incomes'][] = [
                            'subject' => $payLog->subject,
                            'month' => Carbon::parse($payLog->loggable->due_time)->month.'月',
                            'paid_at' => $payLog->paid_at,
                            'amount' => $payLog->amount,
                        ];
                        $roomData['meta']['room_total_income'] += $payLog->amount;

                        // pack expense data relative '仲介費'
                        if ($payLog->subject == '租金') {
                            $firstRentPayment = $tenantContract->tenantPayments->where('subject', '租金')->sortBy('due_time')->first();
                            if ($payLog->loggable->id == $firstRentPayment->id) {
                                $agency_fee = intval(round($payLog->amount * $landlordContract->agency_service_fee));
                                $roomData['expenses'][] = [
                                    'subject' => '仲介費',
                                    'paid_at' => $payLog->paid_at,
                                    'amount' => $agency_fee,
                                ];
                                $data['meta']['total_agency_fee'] += $agency_fee;
                                $roomData['meta']['room_total_expense'] += $agency_fee;
                            }
                        }
                    }

                    // 管理服務費, 科目為『租金服務費』form company_incomes
                    $companyIncomes = $tenantContract->companyIncomes()
                                        ->whereBetween('income_date', [$start_date, $end_date])
                                        ->where('subject', '管理服務費')
                                        ->get();

                    foreach ($companyIncomes as $companyIncome) {
                        $roomData['incomes'][] = [
                            'subject' => '租金服務費',
                            'month' => Carbon::parse($companyIncome->income_date)->month.'月',
                            'paid_at' => $companyIncome->income_date,
                            'amount' => $companyIncome->amount,
                        ];
                        $roomData['meta']['room_total_income'] += $companyIncome->amount;
                    }
                }

                $data['rooms'][] = $roomData;
                $data['meta']['total_income'] += $roomData['meta']['room_total_income'];
                $data['meta']['total_expense'] += $roomData['meta']['room_total_expense'];
            }
            // end section : rooms
        }

        // section: add carry forward
        $carry_forward = MonthlyReport::where('year', $year)
            ->where('month', $month - 1)
            ->where('landlord_contract_id', $landlordContract->id)
            ->get()->first()['carry_forward'] ?: 0;
        if ($carry_forward < 0) {
            $detail_data = [
                'type' => '支出',
                'room_code' => '',
                'subject' => '結轉上期',
                'bill_serial_number' => '',
                'bill_start_date' => '',
                'bill_end_date' => '',
                'paid_at' => $end_date,
                'amount' => $carry_forward,
            ];
            $data['details']['data'][] = $detail_data;
            $data['details']['meta']['total_expenses'] += -$carry_forward;
        } else {
            if ($has_shareholder && $shareholders->first()->distribution_method == '固定') {
                $detail_data = [
                    'type' => '收入',
                    'room_code' => '',
                    'subject' => '結轉上期',
                    'bill_serial_number' => '',
                    'bill_start_date' => '',
                    'bill_end_date' => '',
                    'paid_at' => $end_date,
                    'amount' => $carry_forward,
                ];
                $data['details']['data'][] = $detail_data;
                $data['details']['meta']['total_incomes'] += $carry_forward;
            }
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
            } else {
                $payoffPayments = $tenantContract->tenantPayments
                    ->where('is_pay_off', true)
                    ->where('collected_by', '房東')
                    ->whereBetween('due_time', [$start_date, $end_date]);
                if ($payoffPayments->count() > 0) {
                    $roomData = [
                        'meta' => [
                            'room_total_income' => 0,
                            'room_total_expense' => 0,
                        ],
                        'incomes' => [],
                        'expenses' => []
                    ];
                    $roomData['meta']['room_number'] = $room->room_number;
                    foreach ($payoffPayments as $payoffPayment) {
                        if ($payoffPayment->amount <= 0) {
                            $roomData['incomes'][] = [
                                'subject' => $payoffPayment->subject,
                                'month' => Carbon::parse($payoffPayment->due_time)->month.'月',
                                'paid_at' => $payoffPayment->due_time,
                                'amount' => -$payoffPayment->amount,
                            ];
                            $roomData['meta']['room_total_income'] += -$payoffPayment->amount;
                        } else {
                            $roomData['expenses'][] = [
                                'subject' => $payoffPayment->subject,
                                'month' => Carbon::parse($payoffPayment->due_time)->month.'月',
                                'paid_at' => $payoffPayment->due_time,
                                'amount' => $payoffPayment->amount,
                            ];
                            $roomData['meta']['room_total_expense'] += $payoffPayment->amount;
                        }
                    }
                    $payOffSum = $tenantContract->payOff()->get()->first();
                    $landlordPaid = $payOffSum->landlordPaid;
                    if ($landlordPaid > 0) {
                        $roomData['expenses'][] = [
                            'subject' => '房東應付',
                            'month' => $month.'月',
                            'paid_at' => $payOffSum->created_at,
                            'amount' => $payOffSum->amount,
                        ];
                        $roomData['meta']['room_total_expense'] = $payOffSum->landlordPaid;
                    }
                    $data['payoffs'][] = $roomData;
                    $data['meta']['total_income'] += $roomData['meta']['room_total_income'];
                    $data['meta']['total_expense'] += $roomData['meta']['room_total_expense'];
                }
            }
        }
        // end section : payoffs

        // section : shareholders
        foreach ($landlordContract->building->shareholders as $shareholder) {
            $max_period = $shareholder->distribution_start_date->diffInMonths($shareholder->distribution_end_date) + 1;
            $current_period = $shareholder->distribution_start_date->diffInMonths($start_date) + 1;

            $distribution_fee = 0;
            if ($shareholder->distribution_method == '浮動') {
                $total_revenue = $data['meta']['total_income'] - $data['meta']['total_expense'];
                if ($total_revenue > 0) {
                    $distribution_fee = round($total_revenue * $shareholder->distribution_rate/100);
                }
            } elseif ($shareholder->distribution_method == '固定') {
                $distribution_fee = $shareholder->distribution_amount;
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

    /**
     * @param LandlordContract $landlordContract
     * @param                  $month
     * @param                  $year
     *
     * @return array
     */
    public function getShareholdersInfo(LandlordContract $landlordContract, $month, $year)
    {
        $shareholdersInfo = [];

        foreach ($landlordContract->building->shareholders as $shareholder) {
            $distribution_fee = $this->getDistributionFee($landlordContract, $shareholder, $month, $year);

            $shareholdersInfo[] = [
                'distribution_fee' => $distribution_fee,
            ];
        }

        return $shareholdersInfo;
    }

    private function getDistributionFee($landlordContract, $shareholder, $month, $year)
    {
        $distribution_fee = 0;
        if ($shareholder->distribution_method === '浮動') {
            $total_revenue = $this->getTotalRevenue($landlordContract, $month, $year);
            if ($total_revenue > 0) {
                $distribution_fee = round($total_revenue * $shareholder->distribution_rate/100);
            }
        } elseif ($shareholder->distribution_method === '固定') {
            $distribution_fee = $shareholder->distribution_amount;
        }

        return $distribution_fee;
    }

    /**
     * @param LandlordContract $landlordContract
     * @param                  $month
     * @param                  $year
     *
     * @return int
     */
    private function getTotalRevenue(LandlordContract $landlordContract, $month, $year)
    {
        $total_revenue = 0;
        $data = MonthlyReport::where([
            'year' => $year,
            'month' => $month,
            'landlord_contract_id' => $landlordContract->id,
        ])
            ->first();
        if (! is_null($data)) {
            $total_revenue = $data['carry_forward'];
        }

        return $total_revenue;
    }

    public function getEletricityReport(LandlordContract $landlordContract, $month, $year)
    {
        $data = [
            'meta'         => [],
            'rooms'        => [],
        ];

        $selected_month = Carbon::create($year, $month);

        $data['meta']['year'] = $year;
        $data['meta']['month'] = $month-1;
        $data['meta']['produce_date'] = $selected_month->copy()->subMonth()->endOfMonth()->format('Y/m/d');

        $end_date = $selected_month->copy()->endOfMonth();
        $start_date = $selected_month->copy()->startOfMonth();

        foreach ($landlordContract->building->normalRooms() as $room) {
            $tenantContracts = $room->tenantContracts
                                    ->where('contract_start', '<', $end_date)
                                    ->where('contract_end', '>', $start_date);
            $tenantContractIds = $tenantContracts->pluck('id')->toArray();

            // Find payments data
            $electricity_payments = TenantElectricityPayment::whereIn('tenant_contract_id', $tenantContractIds)->get();
            $current_payment = $electricity_payments->whereBetween('due_time', [$start_date, $end_date])
                                                    ->where(['is_charge_off_done', true])
                                                    ->last();
            $last_unpaid_payments = $electricity_payments->where('due_time', '<', $selected_month->startOfMonth())
                                                        ->where('is_charge_off_done', '=', false);
            $debt = 0;
            foreach ($last_unpaid_payments as $last_unpaid_payment) {
                $debt += $last_unpaid_payment->amount - $last_unpaid_payment->payLogs()->sum('amount');
            }

            // Find pay logs data
            $current_pay_amount = 0;
            $current_pay_logs_dates = ['尚無繳款'];
            if (isset($current_payment)) {
                $current_pay_logs = $current_payment->payLogs();
                $current_pay_amount = $current_pay_logs->sum('amount');
                $current_pay_logs_dates = $current_pay_logs->pluck('paid_at')->toArray();
                $current_pay_logs_dates = array_map(function ($current_pay_logs_date) {
                    return $current_pay_logs_date->format('m-d');
                }, $current_pay_logs_dates);
            } else {
                // Set default value if current_payment not been set
                $current_payment['110v_start_degree'] = $current_payment['110v_end_degree'] = $room->current_110v;
                $current_payment['220v_start_degree'] = $current_payment['220v_end_degree'] = $room->current_220v;
                $current_payment['amount'] = 0;
            }

            // Find electricity degree
            $electricity_price_per_degree = 5.5;
            if ($tenantContracts->isNotEmpty()) {
                if (in_array($selected_month->month, [7, 8, 9, 10])) {
                    $electricity_price_per_degree =  $tenantContracts->last()->electricity_price_per_degree_summer;
                } else {
                    $electricity_price_per_degree =  $tenantContracts->last()->electricity_price_per_degree;
                }
            }

            $data['rooms'][] = [
                'start_110v' => $current_payment['110v_start_degree'],
                'start_220v' => $current_payment['220v_start_degree'],
                'end_110v' => $current_payment['110v_end_degree'],
                'end_220v' => $current_payment['220v_end_degree'],
                'electricity_price_per_degree' =>  $electricity_price_per_degree,
                'current_amount' =>  $current_payment['amount'],
                'debt' => $debt,
                'should_paid' => $debt + $current_payment['amount'],
                'room_number' => $room->room_number,
                'pay_log_amount' => $current_pay_amount,
                'pay_log_date' => implode(',', $current_pay_logs_dates)
            ];
        }

        return $data;
    }
}
