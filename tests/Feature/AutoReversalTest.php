<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\User;

class AutoReversalTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @TODO: Fix test and remove ignore group.
     * @group ignore
     *
     * A basic feature test example.
     *
     * @return void
     */
    public function testBankWebhook()
    {

        $service = app()->make('App\Services\TenantContractService');

        $userId = DB::table('users')->insertGetId([
            'name'              => 'TestUser',
            'email'             => 'ttt@tt.tt',
            'email_verified_at' => Carbon::now()->format('Y-m-d H:i:s'),
        ]);

        $buildingId = DB::table('buildings')->insertGetId([
            'title' => 'test building',
        ]);

        $roomId = DB::table('rooms')->insertGetId([
            'building_id' => $buildingId,
            'room_code' => 'test room',
            'virtual_account' => config('finance.bank_code') . '9216813322423450',
            'management_fee_mode' => '比例',
            'management_fee' => 3,
            'rent_actual' => 5000,
        ]);

        $tenantId = DB::table('tenants')->insertGetId([
            'name' => 'test tenant',
        ]);

        $payments = [
            [
                'subject' => '水雜費',
                'period'  => '季',
                'amount'  => 300,
                'collected_by' => '房東',
            ],
            [
                'subject' => '電費',
                'period'  => '月',
                'amount'  => 100,
                'collected_by' => '公司',
            ]
        ];
        $tenantContractData = [
            'room_id' => $roomId,
            'tenant_id' => $tenantId,
            'contract_serial_number' => 'test tenant contract',
            'contract_start' => '2019-08-10',
            'contract_end' => '2020-08-10',
            'rent' => 5000,
            'commissioner_id' => $userId,
        ];

        $newContract = $service->create($tenantContractData, $payments);

        $data = '<PaySvcRq><PmtAddRq><TDateSeqNo>20100310000029216</TDateSeqNo><TxnDate>20190810</TxnDate><TxnTime>201003</TxnTime><ValueDate>20100310</ValueDate><TxAmount>5401</TxAmount><BankID>0081000</BankID><ActNo>00708804344</ActNo><MAC></MAC><PR_Key1>9216813322423450</PR_Key1></PmtAddRq></PaySvcRq>';
        $response = $this->call('POST', '/api/bank/webhook', [], [], [], [], $data);

        $response->assertStatus(200);

        $firstOfEachPayments = DB::table('tenant_payments')->where('tenant_contract_id', $newContract->id)->groupBy('subject')->get();

        $this->assertDatabaseHas('tenant_payments', [
            'tenant_contract_id' => $newContract->id,
            'subject' => '水雜費',
            'due_time' => '2019-08-10',
            'amount' => 300,
            'is_charge_off_done' => true
        ]);
        $this->assertDatabaseHas('tenant_payments', [
            'tenant_contract_id' => $newContract->id,
            'subject' => '電費',
            'due_time' => '2019-08-10',
            'amount' => 100,
            'is_charge_off_done' => true
        ]);
        $this->assertDatabaseHas('tenant_payments', [
            'tenant_contract_id' => $newContract->id,
            'subject' => '租金',
            'due_time' => '2019-08-10',
            'amount' => 5000,
            'is_charge_off_done' => true
        ]);

        $this->assertDatabaseHas('pay_logs', [
            'loggable_type' => 'App\TenantPayment',
            'loggable_id' => $firstOfEachPayments->where('subject','水雜費')->first()->id,
            'subject' => '水雜費',
            'payment_type' => '租金雜費',
            'amount' => 300,
            'virtual_account' => '9529216813322423450',
        ]);
        $this->assertDatabaseHas('pay_logs', [
            'loggable_type' => 'App\TenantPayment',
            'loggable_id' => $firstOfEachPayments->where('subject','租金')->first()->id,
            'subject' => '租金',
            'payment_type' => '租金雜費',
            'amount' => 5000,
            'virtual_account' => '9529216813322423450',
        ]);
        $this->assertDatabaseHas('pay_logs', [
            'loggable_type' => 'App\TenantPayment',
            'loggable_id' => $firstOfEachPayments->where('subject','電費')->first()->id,
            'subject' => '電費',
            'payment_type' => '電費',
            'amount' => 100,
            'virtual_account' => '9529216813322423450',
        ]);

        $this->assertDatabaseHas('company_incomes', [
            'tenant_contract_id' => $newContract->id,
            'subject' => '租金',
            'income_date' => '2019-08-10',
            'amount' => 150,
        ]);
        $this->assertDatabaseHas('company_incomes', [
            'tenant_contract_id' => $newContract->id,
            'subject' => '電費',
            'income_date' => '2019-08-10',
            'amount' => 100,
        ]);

        $this->assertDatabaseHas('landlord_other_subjects', [
            'subject' => '水雜費',
            'subject_type' => '租金雜費',
            'income_or_expense' => '收入',
            'expense_date' => '2019-08-10',
            'amount' => 300,
            'room_id' => $roomId,
        ]);

        $this->assertTrue(
            User::find($userId)
                ->notifications
                ->contains(function ($value, $key) use ($userId, $newContract){
                    return ($value->notifiable_type === 'App\User')
                        && ($value->notifiable_id === $userId)
                        && ($value->type === 'App\Notifications\AbnormalPaymentReceived')
                        && ($value->data['tenantPayment']['id'] === 18);
                })
        );
    }
}
