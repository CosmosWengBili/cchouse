<?php

namespace Tests\Feature\View;

use App\Deposit;
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
        $deposit = factory(Deposit::class)->create();
        $res = $this->call('GET', route($this->routeName . '.edit', [$deposit->id]));
        $res->assertOk();
    }

    /**
     * test delete
     */
    public function testDestroy()
    {
        $user = factory(User::class)->create();
        $this->be($user);

        $deposit = factory(Deposit::class)->create();
        $res = $this->call('DELETE', route($this->routeName . '.destroy', [$deposit->id]), ["reason"=>"test"]);
        $res->assertOk();
    }

}
