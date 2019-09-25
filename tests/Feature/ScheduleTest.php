<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Console\Scheduling\Event;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;

class ScheduleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Artisan::call('migrate');
        Artisan::call('db:seed');

    }
    /**
     * @TODO: Fix test and remove ignore group.
     * @group ignore
     */
    public function testDailySchedule() {

        Carbon::setTestNow(Carbon::today());

        $schedule = app()->make(Schedule::class);

        $eventNotify = $schedule->events()[0];
        $eventRent = $schedule->events()[1];

        // assert the target events are added
        $this->assertEquals($eventNotify->description, 'Notify contract due in two months');
        $this->assertEquals($eventRent->description, 'Adjust rent');

        $rent_list_price = 1000;
        $rent_landlord = 1500;
        $adjust_ratio = 5.50;

        $userId = DB::table('users')->insertGetId([
            'name' => 'test',
            'email' => 'test@test.test',
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
            'mobile' => '1234'
        ]);

        $landlordId = DB::table('landlords')->insertGetId([
            'name' => 'test landlord',
            'certificate_number' => '123',
            'residence_address' => '',
            'is_legal_person' => 0,
            'is_collected_by_third_party' => 0
        ]);

        $buildingId = DB::table('buildings')->insertGetId([
            'title' => 'test building',
            'city' => '台北市',
            'district' => '信義區',
            'address' => '',
            'tax_number' => '',
            'building_type' => '',
            'floor' => 1,
            'legal_usage' => '',
            'has_elevator' => 1,
            'security_guard' => '',
            'management_count' => '',
            'first_floor_door_opening' => '',
            'public_area_door_opening' => '',
            'room_door_opening' => '',
            'main_ammeter_location' => '',
            'ammeter_serial_number_1' => '',
            'shared_electricity' => '',
            'electricity_payment_method' => '',
            'private_ammeter_location' => '',
            'water_meter_location' => '',
            'water_meter_serial_number' => '',
            'water_payment_method' => '',
            'gas_meter_location' => '',
            'garbage_collection_location' => '',
            'garbage_collection_time' => '',
            'management_fee_payment_method' => '',
            'management_fee_contact' => '',
            'management_fee_contact_phone' => '',
            'distribution_method' => '',
            'administrative_number' => '',
            'accounting_group' => '',
            'rental_receipt' => '',
            'comment' => ''
        ]);

        $roomId = DB::table('rooms')->insertGetId([
            'building_id' => $buildingId,
            'rent_list_price' => $rent_list_price,
            'rent_landlord' => $rent_landlord,
            'needs_decoration' => 1,
            'room_code' => '',
            'virtual_account' => '',
            'room_status' => '',
            'room_number' => '',
            'room_layout' => '',
            'room_attribute' => '',
            'living_room_count' => 1,
            'room_count' => 1,
            'bathroom_count' => 1,
            'parking_count' => 1,
            'rent_reserve_price' => 1,
            'rent_actual' => 1,
            'internet_form' => '',
            'management_fee_mode' => '',
            'management_fee' => 0.1,
            'wifi_account' => '',
            'wifi_password' => '',
            'has_digital_tv' => 0,
            'comment' => '',
        ]);

        $landlordContractId = DB::table('landlord_contracts')->insertGetId([
            'building_id' => $buildingId,
            'commissioner_id' => $userId,
            'commission_end_date' => Carbon::now()->addMonths(2),
            'rent_adjusted_date' => Carbon::now(),
            'adjust_ratio' => $adjust_ratio,
            'commission_type' => '',
            'annual_service_fee_month_count' => 1,
            'charter_fee' => 1,
            'taxable_charter_fee' => 1,
            'rent_collection_frequency' => '',
            'rent_collection_time' => 1,
            'deposit_month_count' => 1,
            'is_collected_by_third_party' => 1,
            'is_notarized' => 1,
        ]);


        // run schedule event
        Artisan::call('schedule:run');
        sleep(2);

        // asserts notification sent successfully
        $this->assertTrue(
            User::find($userId)
                ->notifications
                ->contains(function ($value, $key) use ($userId, $landlordContractId){
                    return ($value->notifiable_type === 'App\User')
                        && ($value->notifiable_id === $userId)
                        && ($value->type === 'App\Notifications\ContractDueInTwoMonths')
                        && ($value->data['landlordContract']['id'] === $landlordContractId);
                })
        );

        // assert that rent were adjusted correctly
        $this->assertDatabaseHas('rooms', [
            'id' => $roomId,
            'rent_list_price' => intval(round($rent_list_price * (100 + $adjust_ratio ) / 100)),
            'rent_landlord' => intval(round($rent_landlord * (100 + $adjust_ratio ) / 100))
        ]);
    }
}
