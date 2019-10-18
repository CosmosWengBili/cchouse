<?php

namespace Tests\Feature\View;

use App\CompanyIncome;
use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CompanyIncomeControllerTest extends TestCase
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

        $this->routeName = 'companyIncomes';
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
        $companyIncome = factory(CompanyIncome::class)->make();
        $res = $this->call('GET', route($this->routeName . '.show', $companyIncome));
        $res->assertOk();
    }

    /**
     * test edit page
     */
    public function testEdit()
    {
        $companyIncome = factory(CompanyIncome::class)->create([
            'incomable_type' => 'App\Maintenance'
        ]);
        $res = $this->call('GET', route($this->routeName . '.edit', [$companyIncome->id]));
        $res->assertOk();
    }

    /**
     * test delete
     */
    public function testDestroy()
    {
        $this->markTestSkipped('Skip');
    }
}
