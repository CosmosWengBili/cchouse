<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use App\TenantElectricityPayment;
use Faker\Generator as Faker;

$factory->define(TenantElectricityPayment::class, function (Faker $faker) {
    $is_charge_off_done = $faker->boolean;

    $due_time = $faker->dateTimeBetween('-10 day', 'now');
    $charge_off_date = null;
    if ($is_charge_off_done) {
        $charge_off_date = $faker->dateTimeBetween('-30 day', 'now');
        $due_time = $faker->dateTimeBetween($charge_off_date->modify('-7 days'), $charge_off_date);
    }

    return [
        'tenant_contract_id' => \App\TenantContract::inRandomOrder()->first(),
        'subject'            => $faker->randomElement(config('enums.tenant_payments.subject')),
        'ammeter_read_date'  => $faker->dateTimeBetween('-15 day', '-10 day'),
        'due_time'           => $due_time,
        '110v_start_degree'  => $faker->randomDigitNotNull,
        '110v_end_degree'    => $faker->randomDigitNotNull,
        '220v_start_degree'  => $faker->randomDigitNotNull,
        '220v_end_degree'    => $faker->randomDigitNotNull,
        'amount'             => $faker->randomDigitNotNull,
        'is_charge_off_done' => $is_charge_off_done,
        'comment'            => $faker->text,
        'created_at'         => $faker->date('Y-m-d H:i:s'),
        'updated_at'         => $faker->date('Y-m-d H:i:s'),
        'charge_off_date'    => $charge_off_date
    ];
});
