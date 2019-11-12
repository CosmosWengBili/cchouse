<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use App\LandlordContract;
use Faker\Generator as Faker;

$factory->define(LandlordContract::class, function (Faker $faker) {
    return [
        'building_id'                       => \App\Building::inRandomOrder()->first(),
        'commissioner_id'                   => \App\User::inRandomOrder()->first(),
        'commission_type'                   => $faker->randomElement(config('enums.landlord_contracts.commission_type')),
        'commission_start_date'             => $faker->dateTimeBetween('-3 years', 'now'),
        'commission_end_date'               => $faker->dateTimeBetween('1 years', '3 years'),
        'warranty_start_date'               => \Carbon\Carbon::now(),
        'warranty_end_date'                 => $faker->dateTimeBetween('now', '2 years'),
        'rental_decoration_free_start_date' => \Carbon\Carbon::now()->subMonth(),
        'rental_decoration_free_end_date'   => $faker->dateTimeBetween('now', '2 years'),
        'annual_service_fee_month_count'    => $faker->randomDigitNotNull,
        'charter_fee'                       => $faker->randomDigitNotNull,
        'taxable_charter_fee'               => $faker->randomDigitNotNull,
        'agency_service_fee'                => $faker->word,
        'rent_collection_frequency'         => $faker->word,
        'rent_collection_time'              => $faker->randomDigitNotNull,
        'rent_adjusted_date'                => $faker->word,
        'adjust_ratio'                      => $faker->randomDigitNotNull,
        'deposit_month_count'               => $faker->randomDigitNotNull,
        'is_collected_by_third_party'       => $faker->word,
        'is_notarized'                      => $faker->boolean,
        'created_at'                        => $faker->date('Y-m-d H:i:s'),
        'updated_at'                        => $faker->date('Y-m-d H:i:s'),
        'can_keep_pets'                     => $faker->boolean,
        'gender_limit'                      => $faker->word,
        'withdrawal_revenue_distribution'   => $faker->randomDigitNotNull
    ];
});

$factory->state(LandlordContract::class, 'new', function ($faker) {
    return [
        'building_id'     => factory(\App\Building::class)->states('new'),
        'commissioner_id' => factory(\App\User::class),
        'commission_type' => \App\User::class,
    ];
});
