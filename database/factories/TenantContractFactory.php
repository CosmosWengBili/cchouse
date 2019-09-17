<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\TenantContract;
use Faker\Generator as Faker;

// this is just a demo of attributes, all relations should be assigned by yourself
$factory->define(TenantContract::class, function (Faker $faker) {
    return [
        'contract_serial_number' => $faker->sentence(),
        'contract_start' => $faker->dateTimeBetween('-1 years', '+1 years'),
        'contract_end' => $faker->dateTimeBetween('+1 years', '+3 years'),
        'rent' => $faker->numberBetween(5000, 20000),

        'room_id' => factory(\App\Room::class)->create(),
        'tenant_id' => factory(\App\Tenant::class)->create()
    ];
});

$factory->afterCreating(TenantContract::class, function ($tenantContract, $faker) {
    $tenantContract['id'] = TenantContract::max('id');
});
