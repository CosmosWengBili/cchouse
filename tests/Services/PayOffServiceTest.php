<?php


use App\Services\PayOffService;
use App\TenantContract;
use App\TenantElectricityPayment;
use App\TenantPayment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class PayOffServiceTest extends TestCase
{
    use RefreshDatabase;

    private $tenantContract;
    private $payOffDate;
    private $payOffService;
    private $tenantElectricityPayment;
    private $tenantPayment;

    protected function setUp(): void
    {
        parent::setUp();
        // disable foreign key constraints
        Schema::disableForeignKeyConstraints();

        // build fake tenant tenant contract
        factory(TenantContract::class)->create(['deposit_paid' => 7777]);
        $this->tenantContract = TenantContract::first();

        // set fake pay off date
        $this->payOffDate = $this->tenantContract->contract_end->subDays(5);

        // build fake tenant electricity payment
        $this->tenantElectricityPayment = factory(TenantElectricityPayment::class)->make();
        $this->tenantContract->tenantElectricityPayments()->save($this->tenantElectricityPayment);

        // build fake tenant payment
        $this->tenantPayment = factory(TenantPayment::class)->make(['due_time' => $this->payOffDate]);
        $this->tenantContract->tenantPayments()->save($this->tenantPayment);

        $this->payOffService = new PayOffService($this->payOffDate, $this->tenantContract);
    }

    public function testBuildPayOffData()
    {
        $data = $this->payOffService->buildPayOffData();
        $endDegreeOf110v = $data["110v_end_degree"];
        $endDegreeOf220v = $data["220v_end_degree"];
        $fees = $data['fees'];

        $this->assertEquals($endDegreeOf110v, $this->tenantElectricityPayment['110v_end_degree']);
        $this->assertEquals($endDegreeOf220v, $this->tenantElectricityPayment['220v_end_degree']);
        $this->assertEquals($fees, [
            [
                'subject' => '履保金',
                'amount' => 7777,
                'comment' => '',
            ],
            [
                'subject' => '電費',
                'amount' => -($this->tenantElectricityPayment->amount),
                'comment' => '',
            ],
            [
                'subject' => $this->tenantPayment->subject,
                'amount' => -($this->tenantPayment->amount),
                'comment' => $this->tenantPayment->comment,
            ]
        ]);
    }

    protected function tearDown(): void
    {
        // re-enable foreign key constraints
        Schema::enableForeignKeyConstraints();
        parent::tearDown();
    }
}
