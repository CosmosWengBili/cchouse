<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use App\CompanyIncome;
use Faker\Generator as Faker;

$factory->define(CompanyIncome::class, function (Faker $faker) {
    return [
        'subject'        => $faker->randomElement(config('enums.tenant_payments.subject')),
        'incomable_id'   => \App\TenantContract::inRandomOrder()->first(),
        'incomable_type' => \App\TenantContract::class,
        'income_date'    => $faker->dateTimeBetween('-15 day', 'now'),
        'amount'         => $faker->randomDigitNotNull,
        'comment'        => $faker->text,
        'created_at'     => $faker->date('Y-m-d H:i:s'),
        'updated_at'     => $faker->date('Y-m-d H:i:s'),
    ];
});
