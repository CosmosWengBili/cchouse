<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Model;
use Faker\Generator as Faker;

$factory->define(\App\MonthlyReport::class, function (Faker $faker) {
    return [
        'landlord_contract_id' => factory(\App\LandlordContract::class),
    ];
});
