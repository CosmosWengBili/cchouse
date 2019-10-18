<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Building;
use Faker\Generator as Faker;

$factory->define(Building::class, function (Faker $faker) {

    $city = $faker->randomElement(array_keys(config('enums.cities')));
    return [
        'title' => $faker->sentence(),
        'building_code' => $faker->randomNumber(),
    ];
});

// after a building is created, make random 1~3 rooms for it
// $factory->afterCreating(Building::class, function ($building, $faker) {
//     $building->rooms()->save(factory(App\Room::class, $faker->numberBetween(1, 3))->create());
// });
