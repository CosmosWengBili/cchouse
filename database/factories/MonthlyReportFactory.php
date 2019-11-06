<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use App\MonthlyReport;
use Faker\Generator as Faker;

$factory->define(MonthlyReport::class, function (Faker $faker) {
    return [
        'landlord_contract_id' => \App\LandlordContract::inRandomOrder()->first(),
        'year'                 => \Carbon\Carbon::now()->year,
        'month'                => \Carbon\Carbon::now()->month,
        'carry_forward'        => $faker->randomDigitNotNull,
        'created_at'           => $faker->date('Y-m-d H:i:s'),
        'updated_at'           => $faker->date('Y-m-d H:i:s'),
    ];
});
