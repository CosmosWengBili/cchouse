<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use App\Receipt;
use Faker\Generator as Faker;

$factory->define(Receipt::class, function (Faker $faker) {
    return [
        'receiptable_id'        => \App\Tenant::inRandomOrder()->first(),
        'receiptable_type'      => \App\Tenant::class,
        'date'                  => $faker->date('Y-m-d H:i:s'),
        'invoice_serial_number' => $faker->randomNumber,
        'invoice_price'         => $faker->word,
        'receiver'              => $faker->word,
        'comment'               => $faker->word,
        'created_at'            => $faker->date('Y-m-d H:i:s'),
        'updated_at'            => $faker->date('Y-m-d H:i:s'),
    ];
});

$factory->state(Receipt::class, 'new', function ($faker) {
    return [
        'receiptable_id'   => factory(\App\Tenant::class)->states('new'),
        'receiptable_type' => \App\Tenant::class,
    ];
});
