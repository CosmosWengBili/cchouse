<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Model;
use Faker\Generator as Faker;

$factory->define(\App\CompanyIncome::class, function (Faker $faker) {
    return [
        'incomable_id' => 1,
        'incomable_type' => factory(\App\TenantContract::class)
    ];
});
