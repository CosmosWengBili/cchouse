<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Room;
use Faker\Generator as Faker;

$factory->define(Room::class, function (Faker $faker) {
    $management_fee_mode = $faker->randomElement(['比例', '固定']);

    return [
        'room_code' => $faker->sentence(),
        'rent_actual' => $faker->numberBetween(5000, 10000),
        'management_fee_mode' => $management_fee_mode,
        'management_fee' => $management_fee_mode === '比例' ? $faker->randomFloat(1, 10) : $faker->randomNumber(100, 500),
    ];
});
