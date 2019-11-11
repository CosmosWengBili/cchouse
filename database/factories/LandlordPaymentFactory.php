<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use App\LandlordPayment;
use Faker\Generator as Faker;

$factory->define(LandlordPayment::class, function (Faker $faker) {
    return [
        'room_id'            => \App\Room::inRandomOrder()->first(),
        'subject'            => $faker->randomElement(config('enums.tenant_payments.subject')),
        'bill_serial_number' => $faker->word,
        'collection_date'    => $faker->dateTimeBetween('-15 day', '15 day'),
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

$factory->state(LandlordPayment::class, 'new', function ($faker) {
    return [
        'room_id' => factory(\App\Room::class)->states('new'),
    ];
});
