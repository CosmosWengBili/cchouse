<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Tenant;
use Faker\Generator as Faker;

$factory->define(Tenant::class, function (Faker $faker) {
    $faker->addProvider(new \Faker\Provider\it_IT\Person($faker));

    return [
        'name'               => $faker->name,
        'certificate_number' => $faker->taxId(),
    ];
});
