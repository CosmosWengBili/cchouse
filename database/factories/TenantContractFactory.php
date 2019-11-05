<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\TenantContract;
use Faker\Generator as Faker;

// this is just a demo of attributes, all relations should be assigned by yourself
$factory->define(TenantContract::class, function (Faker $faker) {
    $rent = $faker->numberBetween(5000, 20000);
    $deposit = $faker->numberBetween($rent, $rent*2-1);

    return [
        'contract_serial_number' => $faker->uuid(),
        'contract_start' => $faker->dateTimeBetween('-1 years', '+1 years'),
        'contract_end' => $faker->dateTimeBetween('+1 years', '+3 years'),
        'rent' => $rent,
        'room_id' => factory(\App\Room::class),
        'tenant_id' => factory(\App\Tenant::class),

        'deposit' => $deposit,
        'deposit_paid' => $deposit,
        'car_parking_floor' => 0,
        'car_parking_space_number' => $faker->postcode,
        'motorcycle_parking_floor' => 0,
        'motorcycle_parking_space_number' => $faker->postcode,
        'motorcycle_parking_count' => 0,
    ];
});
