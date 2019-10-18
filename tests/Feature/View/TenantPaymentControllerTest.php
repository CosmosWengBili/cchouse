<?php

namespace Tests\Feature\View;

use App\TenantContract;
use App\TenantPayment;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\User;

class TenantPaymentControllerTest extends TestCase
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

        $this->routeName = 'tenantPayments';
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
        $tenantPayment = factory(TenantPayment::class)->make();
        $res = $this->call('GET', route($this->routeName . '.show', $tenantPayment));
        $res->assertOk();
    }

    /**
     * test edit page
     */
    public function testEdit()
    {
        factory(TenantContract::class)->create();
        $tenant_contract = TenantContract::latest()->first();
        $tenantPayment = factory(TenantPayment::class)->create([
            'tenant_contract_id' => $tenant_contract->id,
        ]);
        $res = $this->call('GET', route($this->routeName . '.edit', [$tenantPayment->id]));
        $res->assertOk();
    }

    /**
     * test delete
     */
    public function testDestroy()
    {
        factory(TenantContract::class)->create();
        $tenant_contract = TenantContract::latest()->first();
        $tenantPayment = factory(TenantPayment::class)->create([
            'is_charge_off_done' => false,
            'tenant_contract_id' => $tenant_contract->id,
        ]);
        $res = $this->call('DELETE', route($this->routeName . '.destroy', [$tenantPayment->id]));
        $res->assertOk();
    }

}
