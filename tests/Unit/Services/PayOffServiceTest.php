<?php
namespace Tests\Unit\Services;

use App\Building;
use App\Room;
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
    /** @var PayOffService */
    private $payOffService;
    private $tenantElectricityPayment;
    private $tenantPayment;
    private $building;
    private $room;
    private $now;

    protected function setUp(): void
    {
        parent::setUp();

        $this->now = now();

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

        factory(Building::class)->create()->each(function (Building $building) {
            $building->rooms()->save(
                factory(Room::class)->create()
            );
        });
        $this->tenantContract->save([
            'room_id' => Room::first()->id,
        ]);

        $this->tenantContract->building->landlordContracts()->create([
            'commission_type' => '包租',
            'withdrawal_revenue_distribution' => 300,
            'commission_start_date' => $this->now->copy()->subMonth(),
            'commission_end_date' => $this->now->copy()->addWeeks(2),
        ]);
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
        $this->assertEquals($fees['履保金']['amount'], 7777);
        $this->assertEquals($fees['電費']['amount'], -($this->tenantElectricityPayment->amount));

        // TODO: 這邊未來確認商業邏輯正確以後 需要把每個科目的amount計算寫清楚 總共有 包租方式 * 代管方式 = 6種 情境

//        $this->assertEquals($fees['管理費']['amount'], 0);
//        $this->assertEquals($fees['折抵管理費']['amount'], 0);
//        $this->assertEquals($fees['清潔費']['amount'], 0);
//        $this->assertEquals($fees['折抵清潔費']['amount'], 0);
//        $this->assertEquals($fees['滯納金']['amount'], 0);
//        $this->assertEquals($fees['折抵滯納金']['amount'], 0);
//        $this->assertEquals($fees['沒收押金']['amount'], 0);
//        $this->assertEquals($fees['點交中退盈餘分配']['amount'], 0);
//        $this->assertEquals($fees['租金']['amount'], 0);
    }

    protected function tearDown(): void
    {
        // re-enable foreign key constraints
        Schema::enableForeignKeyConstraints();
        parent::tearDown();
    }
}
