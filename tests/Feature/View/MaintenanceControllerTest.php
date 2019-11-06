<?php

namespace Tests\Feature\View;

use App\Maintenance;
use App\Room;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\User;

class MaintenanceControllerTest extends TestCase
{
    use RefreshDatabase;

    private $fakeUser;

    private $routeName;

    protected function setUp(): void
    {
        parent::setUp();

        // create a fake user for testing
        $this->fakeUser = new User(['name' => 'tester']);

        // set the user as login
        $this->be($this->fakeUser);

        $this->routeName = 'maintenances';
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
        $maintenance = factory(Maintenance::class)->make();
        $res = $this->call('GET', route($this->routeName . '.show', $maintenance));
        $res->assertOk();
    }

    /**
     * test edit page
     */
    public function testEdit()
    {
        factory(Room::class)->create();
        $room = Room::latest()->first();
        $maintenance = factory(Maintenance::class)->create([
            'room_id' => $room->id,
        ]);
        $res = $this->call('GET', route($this->routeName . '.edit', [$maintenance->id]));
        $res->assertOk();
    }

    /**
     * test delete
     */
    public function testDestroy()
    {
        factory(Room::class)->create();
        $room = Room::latest()->first();
        $maintenance = factory(Maintenance::class)->create([
            'room_id' => $room->id,
        ]);
        $res = $this->call('DELETE', route($this->routeName . '.destroy', [$maintenance->id]));
        $res->assertOk();
    }

}
