<?php
namespace Tests\Unit\Http\Controllers;

use App\Deposit;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class DepositControllerTest extends TestCase
{
    use RefreshDatabase;

    private $roomId;
    private $roomCode;

    protected function setUp(): void
    {
        parent::setUp();

        $user = factory(User::class)->create(['id' => 1]);
        $this->be($user);
        $buildingId = DB::table('buildings')->insertGetId(['title' => 'test building']);

        $this->roomId = DB::table('rooms')->insertGetId(['building_id' => $buildingId, 'room_code' => 'test room']);
        $this->roomCode = 'test room';
    }

    /** @test */
    public function it_redirect_back_with_error_when_store()
    {
        // 建立已簽約的 deposit
        DB::table('deposits')->insert(['room_id' => $this->roomId, 'is_deposit_collected' => true]);

        $this->post(route('deposits.store'), [
            'room_id' => $this->roomId,
            'deposit_collection_date' => '2019-01-01',
            'deposit_collection_serial_number' => 'test',
            'deposit_confiscated_amount' => 1000,
            'deposit_returned_amount' => 1000,
            'confiscated_or_returned_date' => '2019-12-31',
            'invoicing_amount' => 996,
            'invoice_date' => '2019-01-01',
            'is_deposit_collected' => true,
            'comment' => 'test',
        ]);
        $errors = session('errors')->get('is_deposit_collected');
        $msg = $errors[0];

        $this->assertEquals($msg, "房代碼 {$this->roomCode} 已簽約");
    }
}
