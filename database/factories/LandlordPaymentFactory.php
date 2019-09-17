<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Model;
use Faker\Generator as Faker;

$factory->define(\App\LandlordPayment::class, function (Faker $faker) {
    return [
        'room_id' => factory(\App\Room::class)->create(),
    ];
});
