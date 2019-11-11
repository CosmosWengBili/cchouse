<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use App\Appliance;
use Faker\Generator as Faker;

$factory->define(Appliance::class, function (Faker $faker) {
    return [
        'subject'           => $faker->text(random_int(10, 100)),
        'room_id'           => \App\Room::inRandomOrder()->first(),
        'spec_code'         => chr(rand(65, 90)).str_pad(random_int(1, 999999), 6, '0', STR_PAD_LEFT),
        'vendor'            => $faker->name,
        'count'             => random_int(1, 100),
        'maintenance_phone' => $faker->phoneNumber,
        'comment'           => 'comment here'
    ];
});

$factory->state(Appliance::class, 'new', function ($faker) {
    return [
        'room_id' => factory(\App\Room::class)->states('new'),
    ];
});
