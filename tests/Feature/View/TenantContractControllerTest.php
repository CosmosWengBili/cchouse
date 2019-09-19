<?php

namespace Tests\Feature\View;

use App\TenantContract;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\User;

class TenantContractControllerTest extends TestCase
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

        $this->routeName = 'tenantContracts';
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
        $tenantContract = factory(TenantContract::class)->make();
        $res = $this->call('GET', route($this->routeName . '.show', $tenantContract));
        $res->assertOk();
    }

    /**
     * test edit page
     */
    public function testEdit()
    {
        $tenantContract = factory(TenantContract::class)->create();
        $res = $this->call('GET', route($this->routeName . '.edit', [$tenantContract->id]));
        $res->assertOk();
    }

    /**
     * test delete
     */
    public function testDestroy()
    {
        $tenantContract = factory(TenantContract::class)->create();
        $res = $this->call('DELETE', route($this->routeName . '.destroy', [$tenantContract->id]));
        $res->assertOk();
    }





    public function testExtend()
    {
        $tenantContract = factory(TenantContract::class)->create();
        $res = $this->call('GET', route($this->routeName . '.extend', [$tenantContract->id]));
        $res->assertOk();

    }

    public function testElectricityPaymentReport()
    {
        $tenantContract = factory(TenantContract::class)->create();

        $data = [
            'electricityPaymentReport?' => $tenantContract->id,
            'year?' => 2019,
            'month?' => 8,
        ];
        $res = $this->call('GET', route($this->routeName . '.electricityPaymentReport', $data));
        $res->assertOk();
    }

    public function testPaymentRecheck()
    {
        $tenantContract = factory(TenantContract::class)->create();

        $data = [
            'electricityPaymentReport?' => $tenantContract->id,
        ];
        $res = $this->call('GET', route($this->routeName . '.paymentRecheck', $data));
        $res->assertOk();
    }

}
