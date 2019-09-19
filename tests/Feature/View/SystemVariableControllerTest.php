<?php

namespace Tests\Feature\View;

use App\SystemVariable;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\User;

class SystemVariableControllerTest extends TestCase
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

        $this->routeName = 'system_variables';
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
     * test edit page
     */
    public function testEdit()
    {
        $systemVariable = factory(SystemVariable::class)->create();
        $res = $this->call('GET', route($this->routeName . '.edit', [$systemVariable->id]));
        $res->assertOk();
    }

}
