<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\TenantPayment;
use Faker\Generator as Faker;

// this is just a demo of attributes, all relations should be assigned by yourself
$factory->define(TenantPayment::class, function (Faker $faker) {
    $is_charge_off_done = $faker->boolean;

    $due_time = $faker->dateTimeBetween('-10 day', 'now');

    $charge_off_date = null;
    if ($is_charge_off_done) {
        $charge_off_date = $faker->dateTimeBetween('-30 day', 'now');
        $due_time = $faker->dateTimeBetween($charge_off_date->modify('-7 days'), $charge_off_date);
    }

    return [
        'tenant_contract_id'   => \App\TenantContract::inRandomOrder()->first(),
        'subject'              => $faker->randomElement(config('enums.tenant_payments.subject')),
        'due_time'             => $due_time,
        'amount'               => $faker->randomDigitNotNull,
        'is_charge_off_done'   => $is_charge_off_done,
        'charge_off_date'      => $charge_off_date,
        'collected_by'         => $faker->randomElement(config('enums.tenant_payments.collected_by')),
        'is_visible_at_report' => $faker->boolean,
        'is_pay_off'           => $faker->boolean,
        'comment'              => $faker->text,
        'created_at'           => $faker->date('Y-m-d H:i:s'),
        'updated_at'           => $faker->date('Y-m-d H:i:s'),
        'period'               => $faker->randomElement(config('enums.tenant_payments.period')),
    ];
});

$factory->state(TenantPayment::class, 'new', function ($faker) {
    return [
        'tenant_contract_id' => factory(\App\TenantContract::class)->states('new'),
    ];
});
