<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Model;
use Faker\Generator as Faker;

$factory->define(\App\Key::class, function (Faker $faker) {
    return [
        'keeper_id' => factory(\App\User::class),
        'room_id' => factory(\App\Room::class),
    ];
});
