<?php
namespace Tests;

use App\ContactInfo;
use App\Room;
use App\Tenant;
use App\TenantContract;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Mockery;

class TenantContractTest extends TestCase
{
    use RefreshDatabase;

    private $smsServiceMock;
    private $tenantContract;
    private $mobile = '0912345678';

    protected function setUp(): void
    {
        parent::setUp();
        // ignore foreign key constraints
        Schema::disableForeignKeyConstraints();

        $this->smsServiceMock = Mockery::mock('App\Services\SmsService');
        $this->app->instance('App\Services\SmsService', $this->smsServiceMock);

        // build fake data
        $this->tenantContract = new TenantContract();
        $tenant = Tenant::create();
        $contactInfo = new ContactInfo(['info_type' => 'phone', 'value' => $this->mobile]);
        $room = Room::create();

        $this->tenantContract->room_id = $room->id;
        $tenant->tenantContracts()->save($this->tenantContract);
        $tenant->contactInfos()->save($contactInfo);
    }

    public function testSendElectricityPaymentReportSMS()
    {
        // mock service call
        $this->smsServiceMock
            ->shouldReceive('send')
            ->withArgs([
                $this->mobile,
                '本期總應繳電費為: 0, 電費明細請參考: http://localhost:8000/tenantContracts//electricityPaymentReport/2019/8'
            ])
            ->once();

        // try send sms
        $this->tenantContract->sendElectricityPaymentReportSMS(2019, 8);

        // assert expectation count
        $this->assertEquals(\Mockery::getContainer()->mockery_getExpectationCount(), 1);
    }

    public function tearDown(): void {
        Mockery::close();

        // re-enable foreign key constraints
        Schema::enableForeignKeyConstraints();
    }
}
