<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use App\LandlordPayment;
use Faker\Generator as Faker;

$factory->define(LandlordPayment::class, function (Faker $faker) {
    return [
        'room_id'            => \App\Room::inRandomOrder()->first(),
        'subject'            => $faker->word,
        'bill_serial_number' => $faker->word,
        'collection_date'    => $faker->date('Y-m-d H:i:s'),
        'billing_vendor'     => $faker->word,
        'bill_start_date'    => $faker->date('Y-m-d H:i:s'),
        'bill_end_date'      => $faker->word,
        'amount'             => $faker->randomDigitNotNull,
        'comment'            => $faker->text,
        'is_invoiced'        => $faker->boolean,
        'created_at'         => $faker->date('Y-m-d H:i:s'),
        'updated_at'         => $faker->date('Y-m-d H:i:s'),
    ];
});
