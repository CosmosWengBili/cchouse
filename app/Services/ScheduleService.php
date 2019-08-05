<?php

namespace App\Services;

use Carbon\Carbon;
use App\LandlordContract;
use App\Notifications\ContractDueInTwoMonths;

class ScheduleService {

    protected $method;
    protected $args;

public function __construct($method, $args) {
        $this->method = $method;
        $this->args = $args;
    }

    public function __invoke() {
        // call function dynamically
        $result = call_user_func([$this, $this->method], $this->args);
        // manipulate result, and return
        return $result;
    }

    // returned the packed instance
    public static function make($method, $args = null) {
        return new ScheduleService($method, $args);
    }

    // daily task to notify users that some contracts due in 2 months
    public function notifyContractDueInTwoMonths() {
        LandlordContract::where('commission_end_date', Carbon::today()->addMonth(2))
                        ->with('commissioner')
                        ->get()
                        ->each(function ($landlordContract) {
                            $landlordContract->commissioner->notify(new ContractDueInTwoMonths($landlordContract));
                        });
    }

    // daily task to adjust rent
    public function adjustRent() {
        LandlordContract::with('building.rooms')
                        ->where('rent_adjusted_date', Carbon::today())
                        ->get()
                        ->each(function ($landlordContract) {
                            $landlordContract->building->rooms->each(function ($room) use ($landlordContract) {
                                $room->rent_list_price = intval(round($room->rent_list_price * (100 + $landlordContract->adjust_ratio ) / 100));
                                $room->rent_landlord = intval(round($room->rent_landlord * (100 + $landlordContract->adjust_ratio ) / 100));
                                $room->save();
                            });
                        });
    }


    // public function anotherNotification($data) {
    //     //
    // }

}
