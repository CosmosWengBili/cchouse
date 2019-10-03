<?php

namespace App\Services;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Redis;
use Carbon\Carbon;

use App\User;
use App\LandlordContract;
use App\Landlord;
use App\TenantContract;
use App\TenantPayment;
use App\Maintenance;
use App\MonthlyReport;
use App\SystemVariable;

use App\Notifications\LandlordContractDue;
use App\Notifications\TenantContractDueInTwoMonths;
use App\Notifications\TextNotify;

use App\Services\MonthlyReportService;

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
            'commission_type' => '代管'
        ])
            ->with(['commissioner','building:id,city,district,address','landlords:name'])
            ->get()
            ->each(function ($landlordContract) {
                $landlordContract->commissioner->notify(
                    new LandlordContractDue($landlordContract)
                );
            });
        // charter is 6 months
        LandlordContract::where([
            'commission_end_date' => Carbon::today()->addMonth(6),
            'commission_type' => '包租'
        ])
            ->with(['commissioner','building:id,city,district,address','landlords:name'])
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
                    $ratio = intval($landlordContract->adjust_ratio);
                    $isRatioLTE100 = $ratio <= 100;

                    if ($isRatioLTE100) {
                        // 用 % 數調漲
                        $room->rent_list_price = intval(
                            round(
                                ($room->rent_list_price * (100 + $landlordContract->adjust_ratio) ) / 100
                            )
                        );
                    } else {
                        // 直接將租金加上此值
                        $room->rent_list_price = intval(
                            round(
                                $room->rent_list_price + $landlordContract->adjust_ratio
                            )
                        );
                    }

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
        $landlord_names = Landlord::where(
            'birth','like', '%'.Carbon::today()->addWeek(2)->format('m-d').'%'
        )->pluck('name')->toArray();
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
        $notifyRequiredDays = SystemVariable::where('group', 'Maintenance')
                                            ->where('code', 'MaintenanceNotifyRequiredDays')
                                            ->first()->value;
        $limitDatetime = Carbon::now()->subDays($notifyRequiredDays);
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

    public function genarateDebtCollections()
    {

        $delay = SystemVariable::where('code', 'debt_collection_delay_days')->first('value');
        $delay = $delay ? intval($delay->value) : config('finance.debt_collection_delay_days');
        $notifyAt = Carbon::today()->subDays($delay);
        $tenantPayments = TenantPayment::with('payLogs')->where('is_charge_off_done', false)->where('due_time', $notifyAt)->get();

        foreach ($tenantPayments as $tenantPayment) {
            $tenantPayment->tenantContract->debtCollections()->create([
                'status' => '催收中'
            ]);
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

        // set receipt type for patment '租金'
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

        // set receipt type for patment '電費' with commission type is '代管'
        $landlord_contracts = $landlord_contracts->where('commission_type', '代管');
        foreach( $landlord_contracts as $landlord_contract ){
            $rooms = $landlord_contract->building->rooms;
            foreach( $rooms as $room ){
                foreach( $room->activeContracts as $tenant_contract){
                    $paylogs = $tenant_contract->payLogs()->where('payment_type', '電費')
                                                        ->where('receipt_type', '發票');
                    $paylogs->update(['receipt_type'=>'收據']);
                }
            }
        }
    }

    public static function setMonthlyReportCarryFoward()
    {
        $now = Carbon::now();
        $year = $now->year;
        $month = $now->month;
        $service = new MonthlyReportService();

        $landlordContracts = LandlordContract::where('commission_start_date', '<', Carbon::today())
                                            ->where('commission_end_date', '>', Carbon::today())
                                            ->get();

        foreach( $landlordContracts as $landlordContract ){
            $data = $service->getMonthlyReport( $landlordContract, $month, $year );
            $revenue = $data['meta']['total_income'] - $data['meta']['total_expense'];

            // store carry forward if current day it the last day of the month
            if( Carbon::now()->format('Y-m-d') == Carbon::now()->endOfMonth()->format('Y-m-d') ){
                $monthlyReport = MonthlyReport::create(['year' => $year,
                    'month' => $month,
                    'carry_forward' => $revenue,
                    'landlord_contract_id' => $landlordContract->id]);
            }

            // store to Redis each time
            Redis::set('monthlyRepost:carry:'.$landlordContract->id, $revenue);
        }
    }
    // public function anotherNotification($data) {
    //     //
    // }
}
