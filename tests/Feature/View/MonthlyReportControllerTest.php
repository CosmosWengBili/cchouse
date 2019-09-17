<?php

namespace Tests\Feature\View;

use App\MonthlyReport;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\User;

class MonthlyReportControllerTest extends TestCase
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

        $this->routeName = 'monthlyReports';
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
     * test show page
     */
    public function testShow()
    {
        $monthlyReport = factory(MonthlyReport::class)->make();
        $res = $this->call('GET', route($this->routeName . '.show', $monthlyReport));
        $res->assertOk();
    }

}
