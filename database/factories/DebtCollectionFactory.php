<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use App\DebtCollection;
use Faker\Generator as Faker;

$factory->define(DebtCollection::class, function (Faker $faker) {
    return [
        'tenant_contract_id'   => \App\TenantContract::inRandomOrder()->first(),
        'collector_id'         => \App\User::inRandomOrder()->first(),
        'details'              => $faker->text,
        'status'               => $faker->randomElement(array_keys(config('enums.debt_collections.status'))),
        'is_penalty_collected' => $faker->boolean,
        'comment'              => $faker->text,
        'received_at'          => $faker->date('Y-m-d H:i:s'),
        'created_at'           => $faker->date('Y-m-d H:i:s'),
        'updated_at'           => $faker->date('Y-m-d H:i:s'),
    ];
});

$factory->state(DebtCollection::class, 'new', function ($faker) {
    return [
        'tenant_contract_id' => factory(\App\TenantContract::class)->states('new'),
        'collector_id'       => factory(\App\User::class),
    ];
});
