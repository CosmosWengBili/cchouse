<?php

namespace App\Services;

use Carbon\Carbon;
use App\PayLog;
use App\TenantPayment;
use App\TenantContract;
use App\TenantElectricityPayment;
use App\LandlordContract;
use App\Maintenance;
use App\LandlordOtherSubject;
use App\Deposit;
use App\SystemVariable;
use App\Receipt;

class ReceiptService
{   
    public function makeReceiptData($year, $month)
    {
        $receiptData = array();
        $selectedStartDate = Carbon::create($year, $month);
        $selectedEndDate = $selectedStartDate->copy()->endOfMonth();
        $landlordContracts = LandlordContract::where('commission_end_date', '>', $selectedStartDate)
                                                ->where('commission_start_date', '<', $selectedEndDate)
                                                ->where('commission_type', '包租')
                                                ->with(['landlords', 'building.rooms'])
                                                ->get();

        // Set normal value
        foreach ($landlordContracts as $landlordContract) {
            $data = array();
            // Combine relative room_code value
            $data['building_code'] = $landlordContract->building->building_code;
            $data['group'] = $landlordContract->building->group;
            $data['city'] = $landlordContract->building->city;
            $data['district'] = $landlordContract->building->district;
            $data['address'] = $landlordContract->building->address;
            $data['tax_number'] = $landlordContract->building->tax_number;
            // Combine relative landlord name value
            $data['landlord_name'] = implode(
                '、',
                $landlordContract->landlords->pluck('name')->toArray()
            );
            $taxableCharteFee = $this->countTaxableCharterFee($landlordContract, $year, $month);
            $data['taxable_charter_fee'] = $taxableCharteFee;
            $data['actual_charter_fee'] =
                $this->countActualCharterFee($landlordContract, $taxableCharteFee, $selectedStartDate, $selectedEndDate);
            $data['rent_collection_time'] =
                $landlordContract->rent_collection_time;
            $data['rent_collection_year'] = Carbon::now()->year;
            $data['commission_end_date'] =
                Carbon::create($landlordContract->commission_end_date)->format('Y/m/d');

            array_push($receiptData, $data);
        }

        return $receiptData;
    }

    public function countTaxableCharterFee($landlordContract, $year, $month){
        $commission_start_date = Carbon::parse($landlordContract->commission_start_date);
        $commission_end_date = Carbon::parse($landlordContract->commission_end_date);

        //check whether first commission month of landlordContract
        if( $year == $commission_start_date->year && $month == $commission_start_date->month){
            return $landlordContract->taxable_charter_fee*($commission_start_date->daysInMonth - $commission_start_date->day + 1) / $commission_start_date->daysInMonth; 
        }
        //check whether last commission month of landlordContract
        else if($year == $commission_end_date->year && $month == $commission_end_date->month){
            return $landlordContract->taxable_charter_fee * $commission_start_date->day / $commission_start_date->daysInMonth; 
        }
        else{
            return  $landlordContract->taxable_charter_fee;
        }
    }
    
    public function countActualCharterFee($landlordContract, $this_month_taxable_charter_fee, $start_date, $end_date){

        $rooms = $landlordContract->building->normalRooms();

        $should_paid_amount = 0;
        $paid_amount = 0;
        $should_ignored_amount = 0;

        foreach( $rooms as $room ){
            $tenantContract = $room->activeContracts()->first();
            if(is_null($tenantContract)){}
            else{
                $should_paid_amount += $tenantContract->rent;
                $temp_paid = $tenantContract->tenantPayments->where('is_charge_off_done', True)
                                                        ->where('subject', '租金')
                                                        ->whereBetween('due_time', [$start_date, $end_date])
                                                        ->sum('amount');
                $paid_amount += $temp_paid;
                if($tenantContract->tenant->is_legal_person && $temp_paid != 0){
                    $should_ignored_amount += $temp_paid;
                }
            }
        }
        if( ($should_paid_amount - $should_ignored_amount)/$should_paid_amount > $this_month_taxable_charter_fee ){
            return ($should_paid_amount - $should_ignored_amount)/$should_paid_amount * $this_month_taxable_charter_fee;
        }
        else{
            return ($should_paid_amount - $should_ignored_amount);
        }
        
    }
}
