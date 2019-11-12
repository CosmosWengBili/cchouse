<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Building;
use Faker\Generator as Faker;

$factory->define(Building::class, function (Faker $faker) {
    $faker->addProvider(new \Faker\Provider\at_AT\Payment($faker));
    $city = $faker->randomElement(array_keys(config('enums.cities')));

    return [
        'title'                 => $faker->company,
        'city'                  => $city,
        'building_code'         => $faker->randomNumber(),
        'address'               => $faker->address,
        'tax_number'            => $faker->vat(false),
        'administrative_number' => $faker->randomNumber()
    ];
});
