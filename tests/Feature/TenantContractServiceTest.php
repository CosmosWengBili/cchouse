<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use App\TenantContract;

class TenantContractServiceTest extends TestCase
{

    public function testMakeExtendedContract() {
        $service = app()->make('App\Services\TenantContractService');

        $buildingId = DB::table('buildings')->insertGetId([
            'title' => 'test building',
        ]);

        $roomId = DB::table('rooms')->insertGetId([
            'building_id' => $buildingId,
            'room_code' => 'test room'
        ]);

        $tenantId = DB::table('tenants')->insertGetId([
            'name' => 'test tenant',
        ]);

        // by default adds a month
        $oldContractStart = '2019-10-10';
        $oldContractEnd = '2020-10-10';
        $newContractStart = '2020-10-11';
        $newContractEnd = '2020-11-11';

        $tenantContractId = DB::table('tenant_contract')->insertGetId([
            'room_id' => $roomId,
            'tenant_id' => $tenantId,
            'contract_serial_number' => 'test tenant contract',
            'contract_start' => $oldContractStart,
            'contract_end' => $oldContractEnd,
        ]);

        $oldContract = TenantContract::find($tenantContractId);
        $tempContract = $service->makeExtendedContract($oldContract);
        $tempContract->contract_serial_number = 'modified contract';

        // the temp record shouldn't be in db by now
        $this->assertDatabaseMissing('tenant_contract', [
            'id' => $tenantContractId + 1,
            'contract_serial_number' => 'modified contract',
        ]);

        $tempContract->save();

        // the new record exists and the dates, other attributes and id are correct
        $this->assertDatabaseHas('tenant_contract', [
            'id' => $tenantContractId + 1,
            'room_id' => $roomId,
            'tenant_id' => $tenantId,
            'contract_serial_number' => 'modified contract',
            'contract_start' => $newContractStart,
            'contract_end' => $newContractEnd,
        ]);

    }

    public function testCreate() {
        $service = app()->make('App\Services\TenantContractService');

        $buildingId = DB::table('buildings')->insertGetId([
            'title' => 'test building',
        ]);

        $roomId = DB::table('rooms')->insertGetId([
            'building_id' => $buildingId,
            'room_code' => 'test room'
        ]);

        $tenantId = DB::table('tenants')->insertGetId([
            'name' => 'test tenant',
        ]);

        // some dummy data
        $payments = [
            [
                'subject' => '瓦斯費',
                'period'=>'季',
                'amount'=>100,
                'collected_by'=>'房東'
            ]
        ];
        $tenantContractData = [
            'room_id' => $roomId,
            'tenant_id' => $tenantId,
            'contract_serial_number' => 'test tenant contract',
            'contract_start' => '2019-10-10',
            'contract_end' => '2020-10-10',
            'rent' => 666,
        ];
        $rentShouldPayAt = [
            '2019-10-10',
            '2019-11-10',
            '2019-12-10',
            '2020-01-10',
            '2020-02-10',
            '2020-03-10',
            '2020-04-10',
            '2020-05-10',
            '2020-06-10',
            '2020-07-10',
            '2020-08-10',
            '2020-09-10',
        ];
        $gasShouldPayAt = [
            '2019-10-10',
            '2020-01-10',
            '2020-04-10',
            '2020-07-10',
        ];

        $newContract = $service->create($tenantContractData, $payments);

        // the tenant contract should be created
        $this->assertDatabaseHas('tenant_contract', $tenantContractData);

        // every should-pay rent payment exists
        foreach ($rentShouldPayAt as $date) {
            $this->assertDatabaseHas('tenant_payments', [
                'tenant_contract_id' => $newContract->id,
                'subject' => '租金',
                'due_time' => $date,
                'amount' => $newContract->rent,
                'collected_by' => '公司',
            ]);
        }
        // and the number shuld also match
        $rentPaymentsCount = DB::table('tenant_payments')->where('tenant_contract_id', $newContract->id)->where('subject', '租金')->count();
        $this->assertEquals($rentPaymentsCount, count($rentShouldPayAt));

        // every should-pay gas payment exists
        foreach ($gasShouldPayAt as $date) {
            $this->assertDatabaseHas('tenant_payments', [
                'tenant_contract_id' => $newContract->id,
                'subject' => $payments[0]['subject'],
                'due_time' => $date,
                'amount' => $payments[0]['amount'],
                'collected_by' => $payments[0]['collected_by'],
            ]);
        }
        // and the number shuld also match
        $gasPaymentsCount = DB::table('tenant_payments')->where('tenant_contract_id', $newContract->id)->where('subject', $payments[0]['subject'])->count();
        $this->assertEquals($gasPaymentsCount, count($gasShouldPayAt));

    }
}
