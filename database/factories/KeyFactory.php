<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use App\Key;
use Faker\Generator as Faker;

$factory->define(Key::class, function (Faker $faker) {
    return [
        'keeper_id' => \App\User::inRandomOrder()->first(),
        'room_id'   => \App\Room::inRandomOrder()->first(),
    ];
});

$factory->state(Key::class, 'new', function ($faker) {
    return [
        'room_id'   => factory(\App\Room::class)->states('new'),
        'keeper_id' => factory(\App\User::class),
    ];
});
