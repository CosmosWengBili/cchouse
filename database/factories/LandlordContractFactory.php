<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use App\Model;
use Faker\Generator as Faker;

$factory->define(\App\LandlordContract::class, function (Faker $faker) {
    return [
        'building_id' => factory(\App\Building::class),
        'commissioner_id' => factory(\App\User::class),
        'commission_start_date' => $faker->dateTimeBetween('-3 years', 'now'),
        'commission_end_date' => $faker->dateTimeBetween('1 years', '3 years'),

        'warranty_start_date' => \Carbon\Carbon::now(),
        'warranty_end_date' => $faker->dateTimeBetween('now', '2 years'),
    ];
});
