<?php

namespace Tests\Feature\View;

use App\Building;
use App\User;
use App\Group;
use App\Permission;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BuildingControllerTest extends TestCase
{
    use RefreshDatabase;

    private $fakeUser;

    private $routeName;

    protected function setUp(): void
    {
        parent::setUp();

        // create a fake user for testing
        $this->fakeUser = User::create(['name' => 'tester']);
        $this->fakeGroup = Group::first();
        $this->permission = Permission::where('name', 'delete building');
        $this->fakeUser->assignGroup($this->fakeGroup);
        $this->fakeGroup->givePermissionTo($this->permission);

        // set the user as login
        $this->be($this->fakeUser);

        $this->routeName = 'buildings';
    }

    /**
     * test index page
     */
    public function testIndex()
    {
        $res = $this->call('GET', route($this->routeName . '.index'));
        $res->assertOk();
    }

    /**
     * test create page
     */
    public function testCreate()
    {
        $res = $this->call('GET', route($this->routeName . '.create'));
        $res->assertOk();
    }

    /**
     * test show page
     */
    public function testShow()
    {
        $building = factory(Building::class)->make();
        $res = $this->call('GET', route($this->routeName . '.show', $building));
        $res->assertOk();
    }

    /**
     * test edit page
     */
    public function testEdit()
    {
        $building = factory(Building::class)->create([
            'title' => '我是新物件',
            'city' => '嘉義市',
            'address' => '新物件地址',
            'tax_number' => '123456',
            'building_type' => '公寓',
            'floor' => '1',
            'legal_usage' => '住宅',
            'has_elevator' => '0',
            'security_guard' => 'security_guard',
            'management_count' => '1',
            'first_floor_door_opening' => '鑰匙',
            'public_area_door_opening' => '鑰匙',
            'room_door_opening' => '鑰匙',
            'main_ammeter_location' => '1樓',
            'ammeter_serial_number_1' => '123456',
            'shared_electricity' => '123456',
            'taiwan_electricity_payment_method' => '業主代繳',
            'electricity_payment_method' => '公司代付',
            'private_ammeter_location' => '1F',
            'water_meter_location' => '1F',
            'water_meter_serial_number' => '123',
            'water_payment_method' => '業主代繳',
            'water_meter_reading_date' => '2019-01-01',
            'gas_meter_location' => '1F',
            'garbage_collection_location' => 'outside',
            'garbage_collection_time' => 'anytime',
            'management_fee_payment_method' => '業主自付',
            'management_fee_contact' => 'ME',
            'management_fee_contact_phone' => '23123123212',
            'distribution_method' => '不知道',
            'administrative_number' => '222',
            'accounting_group' => '會計組別111',
            'rental_receipt' => '租金收據222',
        ]);
        $res = $this->call('GET', route($this->routeName . '.edit', [$building->id]));
        $res->assertOk();
    }

    /**
     * test delete
     */
    public function testDestroy()
    {
        $building = factory(Building::class)->create();
        $res = $this->call('DELETE', route($this->routeName . '.destroy', [$building->id]));
        $res->assertOk();
    }

}
