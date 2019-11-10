<?php

namespace Tests\Feature;

use App\CompanyIncome;
use App\PayLog;
use App\TenantElectricityPayment;
use App\TenantPayment;
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
            'electricity_payment_method' => '公司代付'
        ]);

        $roomId = DB::table('rooms')->insertGetId([
            'building_id' => $buildingId,
            'room_code' => 'test room',
            'virtual_account' => config('finance.bank_code') . '9216813322423450',
            'management_fee_mode' => '比例',
            'management_fee' => 3.0,
            'rent_actual' => 5000,
        ]);

        $landlordContractId = DB::table('landlord_contracts')->insertGetId([
            'building_id' => $buildingId,
            'commission_type' => '代管',
            'commission_start_date' => Carbon::now()->subMonth(),
            'commission_end_date' => Carbon::now()->addMonth()
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
            ]
        ];
        $electricityPayments = array_map(function ($tep) {
            return TenantElectricityPayment::make($tep);
        }, [
            [ 'subject' => '電費', 'amount' => 100, 'is_charge_off_done' => false, 'due_time' => '2019-08-10'],
        ]);

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
        $newContract->tenantElectricityPayments()->saveMany($electricityPayments);

        $data = '<PaySvcRq><PmtAddRq><TDateSeqNo>20100310000029216</TDateSeqNo><TxnDate>20190810</TxnDate><TxnTime>201003</TxnTime><ValueDate>20100310</ValueDate><TxAmount>5401</TxAmount><BankID>0081000</BankID><ActNo>00708804344</ActNo><MAC></MAC><PR_Key1>9216813322423450</PR_Key1></PmtAddRq></PaySvcRq>';
        $response = $this->call('POST', '/api/bank/webhook', [], [], [], [], $data);
        $response->assertStatus(200);

        $firstOfEachPayments = DB::table('tenant_payments')->where('tenant_contract_id', $newContract->id)->groupBy('subject')->get();

        $this->assertDatabaseHas('tenant_contract', [
            'id' => $newContract->id,
            'sum_paid' => 5401
        ]);
        $this->assertDatabaseHas('tenant_payments', [
            'tenant_contract_id' => $newContract->id,
            'subject' => '水雜費',
            'due_time' => '2019-08-10',
            'amount' => 300,
            'is_charge_off_done' => true
        ]);
        $this->assertDatabaseHas('tenant_electricity_payments', [
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
            'loggable_type' => 'App\TenantElectricityPayment',
            'loggable_id' => TenantElectricityPayment::first()->id,
            'subject' => '電費',
            'payment_type' => '電費',
            'amount' => 100,
            'virtual_account' => '9529216813322423450',
        ]);

        $this->assertDatabaseHas('company_incomes', [
            'incomable_id' => $newContract->id,
            'incomable_type' => "App\\TenantContract",
            'subject' => '租金服務費',
            'income_date' => '2019-08-10',
            'amount' => 150,
        ]);

        $this->assertDatabaseHas('company_incomes', [
            'incomable_id' => $newContract->id,
            'incomable_type' => "App\\TenantContract",
            'subject' => '電費',
            'income_date' => '2019-08-10',
            'amount' => 100,
        ]);
    }

    public function testAutoReverseNextTenantContract()
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
            'rent_actual' => 100,
        ]);

        $landlordContractId = DB::table('landlord_contracts')->insertGetId([
            'building_id' => $buildingId,
            'commission_type' => '代管',
            'commission_start_date' => Carbon::now()->subMonth(),
            'commission_end_date' => Carbon::now()->addMonth()
        ]);

        $tenantId = DB::table('tenants')->insertGetId([
            'name' => 'test tenant',
        ]);

        $payments = [
            ['subject' => '水雜費', 'period'  => '季', 'amount'  => 300, 'collected_by' => '房東']
        ];
        $electricityPayments = array_map(function ($tep) {
            return TenantElectricityPayment::make($tep);
        }, [
            [ 'subject' => '電費', 'amount' => 100, 'is_charge_off_done' => false, 'due_time' => '2019-08-10'],
        ]);

        $tenantContractData = [
            'room_id' => $roomId,
            'tenant_id' => $tenantId,
            'contract_serial_number' => 'test tenant contract',
            'contract_start' => '2019-08-10',
            'contract_end' => '2020-08-10',
            'rent' => 100,
            'commissioner_id' => $userId,
        ];

        $nextTenantContractData = [
            'room_id' => $roomId,
            'tenant_id' => $tenantId,
            'contract_serial_number' => 'test tenant contract 2',
            'contract_start' => '2020-08-11',
            'contract_end' => '2021-08-11',
            'rent' => 100,
            'commissioner_id' => $userId,
        ];

        $contract = $service->create($tenantContractData, $payments);
        $nextContract = $service->create($nextTenantContractData, $payments);
        $contract->tenantElectricityPayments()->saveMany($electricityPayments);
        $data = '<PaySvcRq><PmtAddRq><TDateSeqNo>20100310000029216</TDateSeqNo><TxnDate>20190810</TxnDate><TxnTime>201003</TxnTime><ValueDate>20100310</ValueDate><TxAmount>5401</TxAmount><BankID>0081000</BankID><ActNo>00708804344</ActNo><MAC></MAC><PR_Key1>9216813322423450</PR_Key1></PmtAddRq></PaySvcRq>';
        $response = $this->call('POST', '/api/bank/webhook', [], [], [], [], $data);

        $response->assertStatus(200);

        $firstOfEachPayments = DB::table('tenant_payments')->where('tenant_contract_id', $contract->id)->groupBy('subject')->get();

        $this->assertDatabaseHas('tenant_contract', [
            'id' => $contract->id,
            'sum_paid' => (
                300 * 4 +  // 四季水雜費
                100 * 12 + // 一年房租
                100 * 1    // 一次電費
            ),
        ]);
        $this->assertDatabaseHas('tenant_payments', [
            'tenant_contract_id' => $contract->id,
            'subject' => '水雜費',
            'due_time' => '2019-08-10',
            'amount' => 300,
            'is_charge_off_done' => true
        ]);
        $this->assertDatabaseHas('tenant_electricity_payments', [
            'tenant_contract_id' => $contract->id,
            'subject' => '電費',
            'due_time' => '2019-08-10',
            'amount' => 100,
            'is_charge_off_done' => true
        ]);
        $this->assertDatabaseHas('tenant_payments', [
            'tenant_contract_id' => $contract->id,
            'subject' => '租金',
            'due_time' => '2019-08-10',
            'amount' => 100,
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
            'amount' => 100,
            'virtual_account' => '9529216813322423450',
        ]);
        $this->assertDatabaseHas('pay_logs', [
            'loggable_type' => 'App\TenantElectricityPayment',
            'loggable_id' => TenantElectricityPayment::first()->id,
            'subject' => '電費',
            'payment_type' => '電費',
            'amount' => 100,
            'virtual_account' => '9529216813322423450',
        ]);
        $this->assertDatabaseHas('company_incomes', [
            'incomable_id' => $contract->id,
            'incomable_type' => "App\\TenantContract",
            'subject' => '租金服務費',
            'income_date' => '2019-08-10',
            'amount' => 3,
        ]);
        $this->assertDatabaseHas('company_incomes', [
            'incomable_id' => $contract->id,
            'incomable_type' => "App\\TenantContract",
            'subject' => '電費',
            'income_date' => '2019-08-10',
            'amount' => 100,
        ]);

        // 下期 Contract 沖銷
        $firstOfEachPayments = DB::table('tenant_payments')->where('tenant_contract_id', $nextContract->id)->groupBy('subject')->get();

        $this->assertDatabaseHas('tenant_contract', [
            'id' => $nextContract->id,
            'sum_paid' => (
                300 * 4 +  // 四季水雜費
                100 * 12   // 一年 次房租
            ),
        ]);
        $this->assertDatabaseHas('tenant_payments', [
            'tenant_contract_id' => $nextContract->id,
            'subject' => '水雜費',
            'due_time' => '2020-08-11',
            'amount' => 300,
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
            'amount' => 100,
            'virtual_account' => '9529216813322423450',
        ]);
        $this->assertDatabaseHas('pay_logs', [
            'loggable_type' => 'App\TenantElectricityPayment',
            'loggable_id' => TenantElectricityPayment::first()->id,
            'subject' => '電費',
            'payment_type' => '電費',
            'amount' => 100,
            'virtual_account' => '9529216813322423450',
        ]);
        $this->assertDatabaseHas('company_incomes', [
            'incomable_id' => $nextContract->id,
            'incomable_type' => "App\\TenantContract",
            'subject' => '租金服務費',
            'income_date' => '2019-08-10',
            'amount' => 3,
        ]);
    }

    public function testGenerateManagementFeeCorrectByRate()
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
            'rent_actual' => 10000,
        ]);
        $landlordContractId = DB::table('landlord_contracts')->insertGetId([
            'building_id' => $buildingId,
            'commission_type' => '代管',
            'commission_start_date' => Carbon::now()->subMonth(),
            'commission_end_date' => Carbon::now()->addMonth()
        ]);
        $tenantId = DB::table('tenants')->insertGetId([ 'name' => 'test tenant']);
        $tenantContractData = [
            'room_id' => $roomId,
            'tenant_id' => $tenantId,
            'contract_serial_number' => 'test tenant contract',
            'contract_start' => '2019-08-10',
            'contract_end' => '2020-08-10',
            'rent' => 10000,
            'commissioner_id' => $userId,
        ];

        $contract = $service->create($tenantContractData, []);
        $data = '<PaySvcRq><PmtAddRq><TDateSeqNo>20100310000029216</TDateSeqNo><TxnDate>20190810</TxnDate><TxnTime>201003</TxnTime><ValueDate>20100310</ValueDate><TxAmount>5401</TxAmount><BankID>0081000</BankID><ActNo>00708804344</ActNo><MAC></MAC><PR_Key1>9216813322423450</PR_Key1></PmtAddRq></PaySvcRq>';
        $response = $this->call('POST', '/api/bank/webhook', [], [], [], [], $data);
        $response->assertStatus(200);
        $firstOfEachPayments = DB::table('tenant_payments')->where('tenant_contract_id', $contract->id)->groupBy('subject')->get();

        $this->assertDatabaseHas('tenant_contract', ['id' => $contract->id, 'sum_paid' => 5401]);
        $this->assertDatabaseHas('tenant_payments', [
            'tenant_contract_id' => $contract->id,
            'subject' => '租金',
            'due_time' => '2019-08-10',
            'amount' => 10000,
            'is_charge_off_done' => false,
        ]);
        $this->assertDatabaseHas('pay_logs', [
            'loggable_type' => 'App\TenantPayment',
            'loggable_id' => $firstOfEachPayments->where('subject','租金')->first()->id,
            'subject' => '租金',
            'payment_type' => '租金雜費',
            'amount' => 5401,
            'virtual_account' => '9529216813322423450',
        ]);
        $this->assertDatabaseHas('company_incomes', [
            'incomable_id' => $contract->id,
            'incomable_type' => "App\\TenantContract",
            'subject' => '租金服務費',
            'income_date' => '2019-08-10',
            'amount' => intval(round(5401 * $contract->room->management_fee / 100)) ,
        ]);
    }

    public function testGenerateManagementFeeCorrectByFixed()
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
            'management_fee_mode' => '固定',
            'management_fee' => 100,
            'rent_actual' => 10000,
        ]);
        $landlordContractId = DB::table('landlord_contracts')->insertGetId([
            'building_id' => $buildingId,
            'commission_type' => '代管',
            'commission_start_date' => Carbon::now()->subMonth(),
            'commission_end_date' => Carbon::now()->addMonth()
        ]);
        $tenantId = DB::table('tenants')->insertGetId([ 'name' => 'test tenant']);
        $tenantContractData = [
            'room_id' => $roomId,
            'tenant_id' => $tenantId,
            'contract_serial_number' => 'test tenant contract',
            'contract_start' => '2019-08-10',
            'contract_end' => '2020-08-10',
            'rent' => 10000,
            'commissioner_id' => $userId,
        ];

        $contract = $service->create($tenantContractData, []);
        $data = '<PaySvcRq><PmtAddRq><TDateSeqNo>20100310000029216</TDateSeqNo><TxnDate>20190810</TxnDate><TxnTime>201003</TxnTime><ValueDate>20100310</ValueDate><TxAmount>5401</TxAmount><BankID>0081000</BankID><ActNo>00708804344</ActNo><MAC></MAC><PR_Key1>9216813322423450</PR_Key1></PmtAddRq></PaySvcRq>';
        $response = $this->call('POST', '/api/bank/webhook', [], [], [], [], $data);
        $response->assertStatus(200);
        $firstOfEachPayments = DB::table('tenant_payments')->where('tenant_contract_id', $contract->id)->groupBy('subject')->get();

        $this->assertDatabaseHas('tenant_contract', ['id' => $contract->id, 'sum_paid' => 5401]);
        $this->assertDatabaseHas('tenant_payments', [
            'tenant_contract_id' => $contract->id,
            'subject' => '租金',
            'due_time' => '2019-08-10',
            'amount' => 10000,
            'is_charge_off_done' => false,
        ]);
        $this->assertDatabaseHas('pay_logs', [
            'loggable_type' => 'App\TenantPayment',
            'loggable_id' => $firstOfEachPayments->where('subject','租金')->first()->id,
            'subject' => '租金',
            'payment_type' => '租金雜費',
            'amount' => 5401,
            'virtual_account' => '9529216813322423450',
        ]);
        $this->assertDatabaseHas('company_incomes', [
            'incomable_id' => $contract->id,
            'incomable_type' => "App\\TenantContract",
            'subject' => '租金服務費',
            'income_date' => '2019-08-10',
            'amount' => intval(round($contract->room->management_fee * (5401 / 10000))),
        ]);
    }

    // 測試產出 `無續約之溢繳入帳`
    public function testGenerateReversalErrorCasesWhenNoNextContract()
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
            'rent_actual' => 100,
        ]);
        $landlordContractId = DB::table('landlord_contracts')->insertGetId([
            'building_id' => $buildingId,
            'commission_type' => '代管',
            'commission_start_date' => Carbon::now()->subMonth(),
            'commission_end_date' => Carbon::now()->addMonth()
        ]);
        $tenantId = DB::table('tenants')->insertGetId([ 'name' => 'test tenant']);
        $tenantContractData = [
            'room_id' => $roomId,
            'tenant_id' => $tenantId,
            'contract_serial_number' => 'test tenant contract',
            'contract_start' => '2019-08-10',
            'contract_end' => '2020-08-10',
            'rent' => 100,
            'commissioner_id' => $userId,
        ];

        $contract = $service->create($tenantContractData, []);
        $data = '<PaySvcRq><PmtAddRq><TDateSeqNo>20100310000029216</TDateSeqNo><TxnDate>20190810</TxnDate><TxnTime>201003</TxnTime><ValueDate>20100310</ValueDate><TxAmount>5401</TxAmount><BankID>0081000</BankID><ActNo>00708804344</ActNo><MAC></MAC><PR_Key1>9216813322423450</PR_Key1></PmtAddRq></PaySvcRq>';
        $response = $this->call('POST', '/api/bank/webhook', [], [], [], [], $data);
        $response->assertStatus(200);
        $firstOfEachPayments = DB::table('tenant_payments')->where('tenant_contract_id', $contract->id)->groupBy('subject')->get();

        $this->assertDatabaseHas('tenant_contract', ['id' => $contract->id, 'sum_paid' => 1200]);
        $this->assertDatabaseHas('tenant_payments', [
            'tenant_contract_id' => $contract->id,
            'subject' => '租金',
            'due_time' => '2019-08-10',
            'amount' => 100,
            'is_charge_off_done' => true,
        ]);
        $this->assertDatabaseHas('pay_logs', [
            'loggable_type' => 'App\TenantPayment',
            'loggable_id' => $firstOfEachPayments->where('subject','租金')->first()->id,
            'subject' => '租金',
            'payment_type' => '租金雜費',
            'amount' => 100,
            'virtual_account' => '9529216813322423450',
        ]);
        $this->assertDatabaseHas('company_incomes', [
            'incomable_id' => $contract->id,
            'incomable_type' => "App\\TenantContract",
            'subject' => '租金服務費',
            'income_date' => '2019-08-10',
            'amount' => intval(round(100 * $contract->room->management_fee / 100)) ,
        ]);

        $this->assertDatabaseHas('reversal_error_cases', [
            'pay_log_id' => PayLog::orderBy('id', 'desc')->first()->id,
            'name' => '無續約之溢繳入帳',
        ]);
    }
}
