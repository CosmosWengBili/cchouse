<?php

use Illuminate\Database\Seeder;
use Faker\Generator as Faker;

class ReversalTestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(Faker $faker)
    {
        // create a building
        $building = factory(App\Building::class)->create();

        // make a room
        $room = factory(App\Room::class)->make([
            'virtual_account' => '9529216813322423450',
        ]);
        // assign room to building
        $building->rooms()->save($room);

        // create a tenant
        $tenant = factory(App\Tenant::class)->create();

        $ts = app()->make('App\Services\TenantContractService');
    
        // some dummy data
        $payments = collect($faker->randomElements(config('finance.reversal'), 2))->map(function($v) use ($faker) {
            return [
                'subject' => $v,
                'period' => $faker->randomElement(['月', '季', '半年', '年']),
                'amount'  => $faker->numberBetween(100, 1000),
                'collected_by' => $faker->randomElement(['公司', '房東']),
            ];
        });
        
        // a more clear way of generating data
        // apply your custom logic here
        // $payments = [
        //     [
        //         'subject' => '水雜費',
        //         'period'  => '季',
        //         'amount'  => $faker->numberBetween(100, 1000),
        //         'collected_by' => '房東',
        //     ],
        //     [
        //         'subject' => '電費',
        //         'period'  => '月',
        //         'amount'  => $faker->numberBetween(100, 1000),
        //         'collected_by' => '公司',
        //     ]
        // ];

        // create tenant contract
        $ts->create([
            'tenant_id' => $tenant->id,
            'room_id' => $room->id,
            'contract_serial_number' => 'test tenant contract',
            'contract_start' => '2019-08-10',
            'contract_end' => '2020-08-10',
            'rent' => $room->rent_actual,
            'commissioner_id' => 1,
        ], $payments);
    }
}
