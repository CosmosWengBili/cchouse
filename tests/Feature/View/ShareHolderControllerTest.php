<?php

namespace Tests\Feature\View;

use App\Shareholder;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\User;

class ShareHolderControllerTest extends TestCase
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

        $this->routeName = 'shareholders';
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
        $shareHolder = factory(Shareholder::class)->make();
        $res = $this->call('GET', route($this->routeName . '.show', $shareHolder));
        $res->assertOk();
    }

    /**
     * test edit page
     */
    public function testEdit()
    {
        $shareHolder = factory(Shareholder::class)->create();
        $res = $this->call('GET', route($this->routeName . '.edit', [$shareHolder->id]));
        $res->assertOk();
    }

    /**
     * test delete
     */
    public function testDestroy()
    {
        $shareHolder = factory(Shareholder::class)->create();
        $res = $this->call('DELETE', route($this->routeName . '.destroy', [$shareHolder->id]));
        $res->assertOk();
    }

}
