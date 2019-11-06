<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Building;
use Faker\Generator as Faker;

$factory->define(Building::class, function (Faker $faker) {
    $faker->addProvider(new \Faker\Provider\at_AT\Payment($faker));

    $city = $faker->randomElement(array_keys(config('enums.cities')));

    return [
        'title' => $faker->company,
        'city' => $city,
        'building_code' => $faker->randomNumber(),
        'address' => $faker->address,
        'tax_number' => $faker->vat(false),
        'administrative_number' => $faker->randomNumber()
    ];
});

// after a building is created, make random 1~3 rooms for it
// $factory->afterCreating(Building::class, function ($building, $faker) {
//     $building->rooms()->save(factory(App\Room::class, $faker->numberBetween(1, 3))->create());
// });
