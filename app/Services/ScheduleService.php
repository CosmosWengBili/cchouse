<?php

namespace App\Services;

use App\Maintenance;
use App\Notifications\TextNotify;
use Carbon\Carbon;
use App\LandlordContract;
use App\Landlord;
use App\Notifications\LandlordContractDue;

class ScheduleService
{
    protected $method;
    protected $args;

    public function __construct($method, $args) {
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

    public function notifyMaintenanceStatus()
    {
        $notifyRequiredDays = 10; # @TODO: Replace with system variable when `System Variable Management` feature done.
        $limitDatetime = Carbon::now()->subDays(10);
        $maintenances = Maintenance::where('status', '!=', 'done')->where('updated_at', '<=', $limitDatetime)->get();
        foreach ($maintenances as $maintenance) {
            $commissioner = $maintenance->commissioner()->first();
            $commissioner->notify(
                new TextNotify("維修清潔單號：{$maintenance->id} 狀態超過 {$notifyRequiredDays} 未更新，煩請抽空查看。")
            );
        }
    }

    // public function anotherNotification($data) {
    //     //
    // }
}
