<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Model;
use Faker\Generator as Faker;

$factory->define(\App\DebtCollection::class, function (Faker $faker) {
    return [
        'tenant_contract_id' => factory(\App\TenantContract::class)
    ];
});
