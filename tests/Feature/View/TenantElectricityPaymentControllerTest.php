<?php

namespace Tests\Feature\View;

use App\TenantContract;
use App\TenantElectricityPayment;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use App\User;
use DB;

class TenantElectricityPaymentControllerTest extends TestCase
{
    use RefreshDatabase;

    private $fakeUser;

    private $routeName;

    protected function setUp(): void
    {
        parent::setUp();
        Schema::disableForeignKeyConstraints();
        DB::table('users')->truncate();

        // create a fake user for testing
        $this->fakeUser = new User(['name' => 'tester']);

        // set the user as login
        $this->be($this->fakeUser);

        $this->routeName = 'tenantElectricityPayments';
    }

    /**
     * test index page
     */
    public function testIndex()
    {
        $res = $this->call('GET', route($this->routeName.'.index'));
        $res->assertOk();
    }

    /**
     * test create page
     */
    public function testCreate()
    {
        $res = $this->call('GET', route($this->routeName.'.create'));
        $res->assertOk();
    }

    /**
     * test show page
     */
    public function testShow()
    {
        $tenantElectricityPayment = factory(TenantElectricityPayment::class)->make();
        $res = $this->call('GET', route($this->routeName.'.show', $tenantElectricityPayment));
        $res->assertOk();
    }

    /**
     * test edit page
     */
    public function testEdit()
    {
        factory(TenantContract::class)->create();
        $tenant_contract = TenantContract::latest()->first();
        $tenantElectricityPayment = factory(TenantElectricityPayment::class)->create([
            'tenant_contract_id' => $tenant_contract->id,
        ]);
        $res = $this->call('GET', route($this->routeName.'.edit', [$tenantElectricityPayment->id]));
        $res->assertOk();
    }

    /**
     * test delete
     */
    public function testDestroy()
    {
        factory(TenantContract::class)->state('new')->create();
        $tenant_contract = TenantContract::latest()->first();
        // 不需要審核
        $tenantElectricityPayment = factory(TenantElectricityPayment::class)->create([
            'tenant_contract_id' => $tenant_contract->id,
            'is_charge_off_done' => 0,
        ]);
        $res = $this->call('DELETE', route($this->routeName.'.destroy', [$tenantElectricityPayment->id]));
        $res->assertOk();

        // 需要審核
        $user = factory(User::class)->create();
        Auth::login($user);
        $tenantElectricityPayment = factory(TenantElectricityPayment::class)->create([
            'tenant_contract_id' => $tenant_contract->id,
            'is_charge_off_done' => 1,
        ]);
        $res = $this->call('DELETE', route($this->routeName.'.destroy', [$tenantElectricityPayment->id]));

        $res->assertStatus(422);
    }
}
