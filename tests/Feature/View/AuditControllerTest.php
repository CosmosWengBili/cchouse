<?php

namespace Tests\Feature\View;

use App\Audit;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\User;

class AuditControllerTest extends TestCase
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

        $this->routeName = 'audits';
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
        $this->markTestSkipped('Skip index function');
    }

    /**
     * test show page
     */
    public function testShow()
    {
        // make a building
        $audit = factory(Audit::class)->make();
        $res = $this->call('GET', route($this->routeName . '.show', $audit));
        $res->assertOk();
    }

    /**
     * test edit page
     */
    public function testEdit()
    {
        $this->markTestSkipped('Skip index function');
    }

    /**
     * test delete
     */
    public function testDestroy()
    {
        $this->markTestSkipped('Skip index function');
    }

}
