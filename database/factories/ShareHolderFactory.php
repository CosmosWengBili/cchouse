<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use App\Model;
use Faker\Generator as Faker;

$factory->define(\App\Shareholder::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'distribution_start_date' => $faker->dateTimeBetween('-3 years', 'now'),
        'distribution_end_date' => $faker->dateTimeBetween('1 years', '3 years'),
    ];
});
