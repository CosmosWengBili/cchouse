<?php

namespace Tests\Feature\View;

use App\Tenant;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\User;

class TenantControllerTest extends TestCase
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

        $this->routeName = 'tenants';
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
        $tenant = factory(Tenant::class)->make();
        $res = $this->call('GET', route($this->routeName . '.show', $tenant));
        $res->assertOk();
    }

    /**
     * test edit page
     */
    public function testEdit()
    {
        $tenant = factory(Tenant::class)->create();
        $res = $this->call('GET', route($this->routeName . '.edit', [$tenant->id]));
        $res->assertOk();
    }

    /**
     * test delete
     */
    public function testDestroy()
    {
        $tenant = factory(Tenant::class)->create();
        $res = $this->call('DELETE', route($this->routeName . '.destroy', [$tenant->id]));
        $res->assertOk();
    }

}
