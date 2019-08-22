<?php

namespace App\Services;
use Illuminate\Support\Collection;

use App\Maintenance;
use App\Notifications\TextNotify;
use Carbon\Carbon;
use App\LandlordContract;
use App\Landlord;
use App\TenantContract;
use App\Notifications\LandlordContractDue;
use App\Notifications\TenantContractDueInTwoMonths;

class ScheduleService
{
    protected $method;
    protected $args;

    public function __construct($method, $args)
    {
        $this->method = $method;
        $this->args = $args;
    }

    public function __invoke()
    {
        // call function dynamically
        $result = call_user_func([$this, $this->method], $this->args);
        // manipulate result, and return
        return $result;
    }

    // returned the packed instance
    public static function make($method, $args = null)
    {
        return new ScheduleService($method, $args);
    }

    // daily task to notify users that some contracts due in 2 months
    public function notifyContractDue()
    {
        // escrow is 2 months
        LandlordContract::where([
            'commission_end_date' => Carbon::today()->addMonth(2),
            'commission_type' => 'escrow'
        ])
            ->with('commissioner')
            ->get()
            ->each(function ($landlordContract) {
                $landlordContract->commissioner->notify(
                    new LandlordContractDue($landlordContract)
                );
            });
        // charter is 6 months
        LandlordContract::where([
            'commission_end_date' => Carbon::today()->addMonth(6),
            'commission_type' => 'charter'
        ])
            ->with('commissioner')
            ->get()
            ->each(function ($landlordContract) {
                $landlordContract->commissioner->notify(
                    new LandlordContractDue($landlordContract)
                );
            });
    }

    // daily task to adjust rent
    public function adjustRent()
    {
        LandlordContract::with('building.rooms')
            ->where('rent_adjusted_date', Carbon::today())
            ->get()
            ->each(function ($landlordContract) {
                $landlordContract->building->rooms->each(function ($room) use (
                    $landlordContract
                ) {
                    $room->rent_list_price = intval(
                        round(
                            ($room->rent_list_price *
                                (100 + $landlordContract->adjust_ratio)) /
                                100
                        )
                    );
                    $room->rent_landlord = intval(
                        round(
                            ($room->rent_landlord *
                                (100 + $landlordContract->adjust_ratio)) /
                                100
                        )
                    );
                    $room->save();
                });
            });
    }

    public function notifyBirth()
    {
        $landlord_names = Landlord::where([
            'birth' => Carbon::today()->addWeek(2)
        ])->pluck('name');
        foreach (User::all() as $key => $user) {
            $user->notify(new TextNotify(implode(" ", $landlord_names)));
        }
    }

    // daily task to notify users that some tenant contracts due in 2 months
    public function notifyTenantContractDueInTwoMonths()
    {
        // escrow is 2 months
        TenantContract::where('contract_end', Carbon::today()->addMonth(2))
            ->with('commissioner')
            ->get()
            ->each(function ($tenantContract) {
                $tenantContract->commissioner->notify(
                    new TenantContractDueInTwoMonths($tenantContract)
                );
            });
    }
    public function notifyMaintenanceStatus()
    {
        $notifyRequiredDays = 10; # @TODO: Replace with system variable when `System Variable Management` feature done.
        $limitDatetime = Carbon::now()->subDays(10);
        $maintenances = Maintenance::where('status', '!=', 'done')
            ->where('updated_at', '<=', $limitDatetime)
            ->get();
        foreach ($maintenances as $maintenance) {
            $commissioner = $maintenance->commissioner()->first();
            $commissioner->notify(
                new TextNotify(
                    "維修清潔單號：{$maintenance->id} 狀態超過 {$notifyRequiredDays} 未更新，煩請抽空查看。"
                )
            );
        }
    }

    public function notifyTenantElectricityPaymentReport()
    {
        $now = Carbon::now();
        $year = $now->year;
        $month = $now->month;

        TenantContract::where('contract_end', '>', $now)
            ->where('electricity_payment_method', '公司代付')
            ->get()
            ->each(function ($tenantContract) use ($year, $month) {
                $tenantContract->sendElectricityPaymentReportSMS($year, $month);
            });
    }

    public static function setReceiptType(){

        $landlord_contracts = LandlordContract::where('commission_start_date', '<', Carbon::today())
                                                ->where('commission_end_date', '>', Carbon::today())
                                                ->with(['building.rooms.activeContracts.payLogs'])->get();

        foreach($landlord_contracts as $contract_key => $landlord_contract){
            if(
                $landlord_contract->commission_type == '包租' &&
                !in_array(
                    true,
                    $landlord_contract->landlords
                        ->pluck('is_legal_person')
                        ->toArray()
                ) 
            ){
                $landlord_pay_logs = new Collection();
                $rooms = $landlord_contract->building->rooms;

                foreach($rooms as $room_key => $room){
                    if( isset($room->activeContracts->first()->tenant) && !$room->activeContracts->first()->tenant->is_legal_person){
                        $landlord_pay_logs = $landlord_pay_logs->merge($room->activeContracts->first()->payLogs->where('subject', '=', '租金'));
                    }
                }
    
                $first_day_of_last_month = Carbon::today()->subMonth()->startOfMonth();
                $first_day_of_this_month = Carbon::today()->startOfMonth();
                $last_month_pay = 0;
                $this_month_pay = 0;

                foreach($landlord_pay_logs as $pay_log_key => $landlord_pay_log){
                    if( $landlord_pay_log['paid_at'] >= $first_day_of_last_month && $landlord_pay_log['paid_at'] < $first_day_of_this_month ){
                        $last_month_pay += $landlord_pay_log['amount'];
                    }
                    else if( $landlord_pay_log['paid_at'] >= $first_day_of_this_month  ){
                        $this_month_pay += $landlord_pay_log['amount'];
                    }

                    if($last_month_pay > $landlord_contract['taxable_charter_fee'] || $this_month_pay > $landlord_contract['taxable_charter_fee']) {
                        $landlord_pay_log->update(['receipt_type'=>'發票']);
                    }
                    else{
                        $landlord_pay_log->update(['receipt_type'=>'收據']);
                    }
                }

            }
        }
    }

    // public function anotherNotification($data) {
    //     //
    // }
}
