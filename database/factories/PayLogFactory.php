<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use App\PayLog;
use Faker\Generator as Faker;

$factory->define(PayLog::class, function (Faker $faker) {
    return [
        'tenant_contract_id' => \App\TenantContract::inRandomOrder()->first(),
        'subject'            => $faker->randomElement(config('enums.tenant_payments.subject')),
        'loggable_type'   => \App\TenantPayment::class,
        'loggable_id'     => \App\TenantPayment::inRandomOrder()->first(),
        'payment_type'    => $faker->randomElement(config('enums.pay_logs.payment_type')),
        'amount'          => $faker->randomDigitNotNull,
        'virtual_account' => $faker->word,
        'receipt_type'    => $faker->word,
        'comment'         => $faker->text,
        'paid_at'         => $faker->date('Y-m-d H:i:s'),
        'created_at'      => $faker->date('Y-m-d H:i:s'),
        'updated_at'      => $faker->date('Y-m-d H:i:s'),
    ];
});

$factory->state(PayLog::class, 'new', function ($faker) {
    return [
        'tenant_contract_id' => factory(\App\TenantContract::class)->states('new'),
        'loggable_id'        => factory(\App\TenantPayment::class)->states('new'),
        'loggable_type'      => \App\TenantPayment::class,
    ];
});
