<?php

namespace Tests\Feature\View;

use App\Appliance;
use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ApplianceControllerTest extends TestCase
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

        $this->routeName = 'appliances';
    }

    /**
     * test index page
     */
    public function testIndex()
    {
        $this->markTestSkipped('Skip index function');
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
        $this->markTestSkipped('Skip show function');
    }

    /**
     * test edit page
     */
    public function testEdit()
    {
        $appliance = factory(Appliance::class)->create();
        $res = $this->call('GET', route($this->routeName . '.edit', [$appliance->id]));
        $res->assertOk();
    }

    /**
     * test delete
     */
    public function testDestroy()
    {
        $appliance = factory(Appliance::class)->create();
        $res = $this->call('DELETE', route($this->routeName . '.destroy', [$appliance->id]));
        $res->assertOk();
    }

}
