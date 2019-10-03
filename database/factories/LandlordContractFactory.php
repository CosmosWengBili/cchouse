<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Model;
use Faker\Generator as Faker;

$factory->define(\App\LandlordContract::class, function (Faker $faker) {
    return [
        'building_id' => factory(\App\Building::class),
        'commissioner_id' => factory(\App\User::class),
    ];
});
