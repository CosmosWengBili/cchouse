<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Model;
use Faker\Generator as Faker;

$factory->define(\App\Maintenance::class, function (Faker $faker) {
    return [
        'commissioner_id' => factory(\App\User::class),
        'maintenance_staff_id' => factory(\App\User::class),
        'tenant_contract_id' => factory(\App\TenantContract::class),
    ];
});
