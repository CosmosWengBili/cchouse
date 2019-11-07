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
        $this->createApplication();

        Artisan::call('migrate');
        Artisan::call('db:seed');
    }
    /**
     * @TODO: Fix test and remove ignore group.
     * @group ignore
     */
    public function testDailySchedule()
    {
        Carbon::setTestNow(Carbon::create(2019, 8, 31));

        $schedule = app()->make(Schedule::class);

        $eventNotify = $schedule->events()[0];
        $eventRent = $schedule->events()[1];

        // assert the target events are added
        $this->assertEquals($eventNotify->description, 'Notify contract due in two months');
        $this->assertEquals($eventRent->description, 'Adjust rent');

        $rent_reserve_price = 1000;
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
            'taiwan_electricity_payment_method' => '',
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
            'room_code' => '',
            'virtual_account' => '',
            'room_status' => '',
            'room_number' => '',
            'room_layout' => '',
            'living_room_count' => 1,
            'room_count' => 1,
            'bathroom_count' => 1,
            'parking_count' => 1,
            'rent_reserve_price' => $rent_reserve_price,
            'rent_actual' => 1,
            'internet_form' => '',
            'management_fee_mode' => '',
            'management_fee' => 0.1,
            'wifi_account' => '',
            'wifi_password' => '',
            'has_digital_tv' => 0,
            'comment' => '',
        ]);

        $buildingCharterId = DB::table('buildings')->insertGetId([
            'title' => 'test building for charter',
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
            'taiwan_electricity_payment_method' => '',
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

        $roomIds = [];
        for ($k = 0 ; $k < 5; $k++) {
            $roomIds[] = DB::table('rooms')->insertGetId([
                'building_id' => $buildingCharterId,
                'room_code' => '',
                'virtual_account' => '',
                'room_status' => '',
                'room_number' => '',
                'room_layout' => '',
                'living_room_count' => 1,
                'room_count' => 1,
                'bathroom_count' => 1,
                'parking_count' => 1,
                'rent_reserve_price' => $rent_reserve_price,
                'rent_actual' => 1,
                'internet_form' => '',
                'management_fee_mode' => '',
                'management_fee' => 0.1,
                'wifi_account' => '',
                'wifi_password' => '',
                'has_digital_tv' => 0,
                'comment' => '',
            ]);
        }

        $landlordContractId = DB::table('landlord_contracts')->insertGetId([
            'building_id' => $buildingId,
            'commissioner_id' => $userId,
            'commission_end_date' => Carbon::now()->addMonths(2),
            'rent_adjusted_date' => Carbon::now(),
            'adjust_ratio' => $adjust_ratio,
            'commission_type' => '代管',
            'annual_service_fee_month_count' => 1,
            'charter_fee' => 1,
            'taxable_charter_fee' => 1,
            'rent_collection_frequency' => '',
            'rent_collection_time' => 1,
            'deposit_month_count' => 1,
            'is_collected_by_third_party' => 1,
            'is_notarized' => 1,
        ]);

        $landlordContractCharterId = DB::table('landlord_contracts')->insertGetId([
            'building_id' => $buildingCharterId,
            'commissioner_id' => $userId,
            'commission_start_date' => Carbon::create(2019, 1, 10),
            'commission_end_date' => Carbon::create(2020, 1, 10),
            'rent_adjusted_date' => Carbon::create(2022, 1, 10),
            'adjust_ratio' => $adjust_ratio,
            'commission_type' => '包租',
            'annual_service_fee_month_count' => 1,
            'charter_fee' => 1,
            'taxable_charter_fee' => 40000,
            'rent_collection_frequency' => '',
            'rent_collection_time' => 1,
            'deposit_month_count' => 1,
            'is_collected_by_third_party' => 1,
            'is_notarized' => 1,
        ]);

        $payLogIds = [];
        foreach ([false, false, false, false, true] as $key => $is_legal_person) {
            $tenantId = DB::table('tenants')->insertGetId([
                'is_legal_person' => $is_legal_person,
                'certificate_number' => 'id'.rand(0, 10000)
            ]);
            $tenantContractId = DB::table('tenant_contract')->insertGetId([
                'room_id' => $roomIds[$key],
                'tenant_id' => $tenantId,
                'contract_start' => '2019-02-02',
                'contract_end' => '2019-12-02',
            ]);
            $tenantPaymentId = DB::table('tenant_payments')->insertGetId([
                'is_charge_off_done' => true,
                'subject' => '租金',
                'due_time' => Carbon::create(2019, 8, rand(1, 31)),
                'amount' => 12000,
                'tenant_contract_id' => $tenantContractId
            ]);

            $payLogIds[] = DB::table('pay_logs')->insertGetId([
                'receipt_type' => '發票',
                'loggable_type' => 'App\TenantPayment',
                'loggable_id' => $tenantPaymentId,
            ]);
        }

        // run schedule event
        Artisan::call('schedule:run');
        sleep(2);

        // run set receipt schedule
        Carbon::setTestNow(Carbon::create(2019, 8, 31, 4, 30, 0));
        Artisan::call('schedule:run');
        sleep(2);

        // asserts notification sent successfully
        $this->assertTrue(
            User::find($userId)
                ->notifications
                ->contains(function ($value, $key) use ($userId) {
                    return ($value->notifiable_type === 'App\User')
                        && ($value->notifiable_id === $userId)
                        && ($value->type === 'App\Notifications\LandlordContractDue')
                        && (strpos($value->data['content'], '合約即將到期! 物件編號') == 0);
                })
        );

        // assert that rent were adjusted correctly
        $this->assertDatabaseHas('rooms', [
            'id' => $roomId,
            'rent_reserve_price' => intval(round($rent_reserve_price * (100 + $adjust_ratio) / 100)),
        ]);

        // assert that paylogs were adjusted correctly
        $this->assertDatabaseHas('pay_logs', [
            'id' => $payLogIds[0],
            'receipt_type' => '收據'
        ]);
        $this->assertDatabaseHas('pay_logs', [
            'id' => $payLogIds[3],
            'receipt_type' => '發票'
        ]);
        $this->assertDatabaseHas('pay_logs', [
            'id' => $payLogIds[4],
            'receipt_type' => '發票'
        ]);
    }
}
