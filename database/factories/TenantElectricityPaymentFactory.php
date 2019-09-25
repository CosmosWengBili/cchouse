<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\TenantContract;
use App\TenantElectricityPayment;
use Faker\Generator as Faker;

// this is just a demo of attributes, all relations should be assigned by yourself
$factory->define(TenantElectricityPayment::class, function (Faker $faker) {
    return [
        'tenant_contract_id' => factory(TenantContract::class)->create(),
        'ammeter_read_date' => '2019-08-18',
        '110v_start_degree' => 0,
        '110v_end_degree' => 100,
        '220v_start_degree' => 0,
        '220v_end_degree' => 220,
        'amount' => 1815,
        'is_charge_off_done' => 0,
        'comment' => '',
        'due_time' => '2019-08-18',
    ];
});
