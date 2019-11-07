<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\TenantContract;
use App\TenantElectricityPayment;
use App\TenantPayment;
use Faker\Generator as Faker;

// this is just a demo of attributes, all relations should be assigned by yourself
$factory->define(TenantPayment::class, function (Faker $faker) {
    return [
        'tenant_contract_id' => 0,
        'subject' => '水雜費',
        'due_time' => '2019-08-10',
        'amount' => 300,
        'is_charge_off_done' => true,
        'charge_off_date' => '2019-08-01',
        'collected_by' => '公司',
        'is_visible_at_report' => false,
        'is_pay_off' => true,
        'comment' => '欠繳水雜費',
        'period' => '',
    ];
});
