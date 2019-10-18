<?php

namespace Tests\Feature\View;

use App\Deposit;
use App\TenantContract;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\User;

class DepositControllerTest extends TestCase
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

        $this->routeName = 'deposits';
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
        $deposit = factory(Deposit::class)->make();
        $res = $this->call('GET', route($this->routeName . '.show', $deposit));
        $res->assertOk();
    }

    /**
     * test edit page
     */
    public function testEdit()
    {
        factory(TenantContract::class)->create();
        $tenant_contract = TenantContract::latest()->first();
        $deposit = factory(Deposit::class)->create([
            'tenant_contract_id' => $tenant_contract->id,
        ]);
        $res = $this->call('GET', route($this->routeName . '.edit', [$deposit->id]));
        $res->assertOk();
    }

}
