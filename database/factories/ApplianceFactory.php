<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Model;
use Faker\Generator as Faker;

$factory->define(\App\Appliance::class, function (Faker $faker) {
    return [
        'subject' => $faker->text(random_int(10, 100)),
        'room_id' => factory(\App\Room::class),
        'spec_code' =>chr(rand(65,90)) . str_pad(random_int(1, 999999), 6, '0', STR_PAD_LEFT),
        'vendor' => $faker->name,
        'count' => random_int(1,100),
        'maintenance_phone' => $faker->phoneNumber,
        'comment' => 'comment here'
    ];
});
