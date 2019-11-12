<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use App\TenantElectricityPayment;
use Faker\Generator as Faker;

$factory->define(TenantElectricityPayment::class, function (Faker $faker) {
    $is_charge_off_done = $faker->boolean;

    $due_time = $faker->dateTimeBetween('-10 day', 'now');
    $charge_off_date = null;

    $star_110v = $faker->randomFloat(0);
    $end_110v = $faker->randomFloat(0, $star_110v, null);

    $star_220v = $faker->randomFloat(0);
    $end_220v = $faker->randomFloat(0, $star_220v, null);

    return [
        'tenant_contract_id' => \App\TenantContract::inRandomOrder()->first(),
        // 'subject'            => $faker->randomElement(config('enums.tenant_payments.subject')),
        'ammeter_read_date'  => $faker->dateTimeBetween('-15 day', '-10 day'),
        'due_time'           => $due_time,
        '110v_start_degree'  => $star_110v,
        '110v_end_degree'    => $end_110v,
        '220v_start_degree'  => $star_220v,
        '220v_end_degree'    => $end_220v,
        'amount'             => $faker->numberBetween(0, 9999),
        'is_charge_off_done' => 0,
        'comment'            => $faker->text,
        'created_at'         => $faker->date('Y-m-d H:i:s'),
        'updated_at'         => $faker->date('Y-m-d H:i:s'),
        'charge_off_date'    => $charge_off_date
    ];
});

$factory->state(TenantElectricityPayment::class, 'new', function ($faker) {
    return [
        'tenant_contract_id' => factory(\App\TenantContract::class)->states('new'),
    ];
});

$factory->state(TenantElectricityPayment::class, 'is_charge_off_done', function ($faker) {
    $charge_off_date = $faker->dateTimeBetween('-30 day', 'now');
    $due_time = $faker->dateTimeBetween($charge_off_date->modify('-7 days'), $charge_off_date);

    return [
        'is_charge_off_done' => 1,
        'charge_off_date'    => $charge_off_date,
        'due_time'           => $due_time,
    ];
});
