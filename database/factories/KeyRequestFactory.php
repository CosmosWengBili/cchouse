<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use App\KeyRequest;
use Faker\Generator as Faker;

$factory->define(KeyRequest::class, function (Faker $faker) {
    return [
        'key_id'          => \App\Key::inRandomOrder()->first(),
        'request_user_id' => \App\User::inRandomOrder()->first()
    ];
});

$factory->state(KeyRequest::class, 'new', function ($faker) {
    return [
        'key_id'          => factory(\App\Key::class)->states('new'),
        'request_user_id' => factory(\App\User::class),
    ];
});
