<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\TenantContract;
use Faker\Generator as Faker;

// this is just a demo of attributes, all relations should be assigned by yourself
$factory->define(TenantContract::class, function (Faker $faker) {
    $rent = $faker->numberBetween(5000, 20000);
    $deposit = $faker->numberBetween($rent, $rent * 2 - 1);

    return [
        'room_id'                             => \App\Room::inRandomOrder()->first(),
        'tenant_id'                           => \App\Tenant::inRandomOrder()->first(),
        'commissioner_id'                     => \App\User::inRandomOrder()->first(),
        'contract_serial_number'              => $faker->uuid(),
        'set_other_rights'                    => $faker->word,
        'other_rights'                        => $faker->word,
        'sealed_registered'                   => $faker->word,
        'car_parking_floor'                   => $faker->randomDigitNotNull,
        'car_parking_type'                    => $faker->word,
        'car_parking_space_number'            => $faker->postcode,
        'motorcycle_parking_floor'            => $faker->randomDigitNotNull,
        'motorcycle_parking_space_number'     => $faker->postcode,
        'motorcycle_parking_count'            => $faker->randomDigitNotNull,
        'contract_start'                      => $faker->dateTimeBetween('-1 years', '+1 years'),
        'contract_end'                        => $faker->dateTimeBetween('+1 years', '+3 years'),
        'rent'                                => $rent,
        'rent_pay_day'                        => $faker->randomDigitNotNull,
        'deposit'                             => $deposit,
        'deposit_paid'                        => $deposit,
        'electricity_calculate_method'        => $faker->word,
        'electricity_price_per_degree'        => $faker->randomDigitNotNull,
        'electricity_price_per_degree_summer' => $faker->randomDigitNotNull,
        '110v_start_degree'                   => $faker->randomDigitNotNull,
        '220v_start_degree'                   => $faker->randomDigitNotNull,
        '110v_end_degree'                     => $faker->randomDigitNotNull,
        '220v_end_degree'                     => $faker->randomDigitNotNull,
        'invoice_collection_method'           => $faker->word,
        'invoice_collection_number'           => $faker->word,
        'comment'                             => $faker->text,
        'sum_paid'                            => $faker->randomDigitNotNull,
        'overdue_fine'                        => $faker->randomDigitNotNull,
        'created_at'                          => $faker->date('Y-m-d H:i:s'),
        'updated_at'                          => $faker->date('Y-m-d H:i:s'),
    ];
});
