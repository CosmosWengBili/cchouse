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
    public $globalData;

    public function makeReceiptData($year, $month)
    {
        $this->globalData = [
            'receipt' => [], 'receipt_building' => []
        ];
        $selectedStartDate = Carbon::create($year, $month);
        $selectedEndDate = $selectedStartDate->copy()->endOfMonth();
        $landlordContracts = LandlordContract::where('commission_end_date', '>', $selectedStartDate)
                                                ->where('commission_start_date', '<', $selectedEndDate)
                                                ->where('commission_type', '包租')
                                                ->with(['landlords', 'building.rooms'])
                                                ->get();

        // Set normal value
        foreach ($landlordContracts as $landlordContract) {
            $rooms = $landlordContract->building->normalRooms();
            foreach( $rooms as $room ){
                $tenantContracts = $room->activeContracts()->get();
                foreach( $tenantContracts as $tenantContract){
                    $payments = $tenantContract->tenantPayments->where('is_charge_off_done', True)
                                                                ->where('subject', '租金')
                                                                ->whereBetween('due_time', [$selectedStartDate, $selectedEndDate])
                                                                ;
                    if( !$tenantContract->tenant->is_legal_person && $payments->sum('amount') != 0){
                        foreach( $payments as $payment){
                            $data = array();
                            $data['building_code'] = $room->building->building_code;
                            $data['room_number'] = $room->room_number;
                            $data['tenant_name'] = $tenantContract->tenant->name;
                            $data['paid_at'] = $payment->due_time;
                            $data['amount'] = $payment->amount;
                            $data['group'] = $room->building->group;

                            array_push($this->globalData['receipt'], $data);
                        }
                    }
                }
            }            
        }

        $this->makeReceiptBuildingData($landlordContracts, $selectedStartDate, $selectedEndDate);
        return $this->globalData;
    }

    public function makeReceiptBuildingData($landlordContracts, $selectedStartDate, $selectedEndDate){
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
            
            $data['taxable_charter_fee'] = $landlordContract->taxable_charter_fee;
            $taxableCharteFee = $this->countTaxableCharterFee($landlordContract, $selectedStartDate->year, $selectedStartDate->month);
            $data['actual_charter_fee'] =
                round($this->countActualCharterFee($landlordContract, $taxableCharteFee, $selectedStartDate, $selectedEndDate));
            $data['rent_collection_time'] =
                $landlordContract->rent_collection_time;
            $data['rent_collection_year'] = Carbon::now()->year;
            $data['commission_end_date'] =
                Carbon::create($landlordContract->commission_end_date)->format('Y/m/d');

            array_push($this->globalData['receipt_building'], $data);
        }
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
        $should_paid_amount = $rooms->sum('rent_actual');
        $should_ignored_amount = 0;
        foreach( $rooms as $room ){
            $tenantContracts = $room->activeContracts()->get();
	        foreach( $tenantContracts as $tenantContract){
                if(is_null($tenantContract)){}
                else{
                    if($tenantContract->tenant->is_legal_person){
                        $should_ignored_amount += $room->rent_actual;
                    }
                }
            }
        }
        $valid_amount = $should_paid_amount - $should_ignored_amount;
        if( $valid_amount > $this_month_taxable_charter_fee ){
            return $this_month_taxable_charter_fee;
        }
        else{
            return $valid_amount/$should_paid_amount * $this_month_taxable_charter_fe;
        }
    }
}
