<?php

namespace Tests\Feature\View;

use App\Receipt;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\User;

class ReceiptControllerTest extends TestCase
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

        $this->routeName = 'receipts';
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
        $this->markTestSkipped('There are no create in web.php');
    }

    /**
     * test show page
     */
    public function testShow()
    {
        $this->markTestSkipped('There are no show in web.php');
    }

    /**
     * test edit page
     */
    public function testEdit()
    {
        $this->markTestSkipped('There are no edit in web.php');
    }

    public function testEditInvoice()
    {
        $date = [
            'start_date' => '2019-08-01',
            'end_date'   => '2019-09-31',
        ];
        $res = $this->call('GET', route($this->routeName . '.edit_invoice', $date));
        $res->assertOk();
    }

    /**
     * test delete
     */
    public function testDestroy()
    {
        $this->markTestSkipped('There are no destroy in web.php');
    }
}
