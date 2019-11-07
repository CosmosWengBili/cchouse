<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use App\Model;
use Faker\Generator as Faker;

$factory->define(\App\Landlord::class, function (Faker $faker) {
    $faker->addProvider(new \Faker\Provider\it_IT\Person($faker));

    return [
        'name' => $faker->name(),
        'certificate_number' => $faker->taxId(),
    ];
});
