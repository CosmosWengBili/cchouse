<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Model;
use Faker\Generator as Faker;

$factory->define(\App\KeyRequest::class, function (Faker $faker) {
    return [
        'key_id' => factory(\App\Key::class),
        'request_user_id' => factory(\App\User::class)
    ];
});
