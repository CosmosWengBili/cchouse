<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use App\Model;
use Faker\Generator as Faker;

$factory->define(\App\CompanyIncome::class, function (Faker $faker) {
    return [
        'subject' => $faker->text(random_int(10, 100)),
        'incomable_id' => 1,
        'incomable_type' => \App\TenantContract::class
    ];
});
