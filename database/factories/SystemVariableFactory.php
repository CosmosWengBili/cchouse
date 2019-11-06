<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use App\SystemVariable;
use Faker\Generator as Faker;

$factory->define(SystemVariable::class, function (Faker $faker) {
    return [
        // 'code'       => $faker->word,
        // 'value'      => $faker->word,
        // 'group'      => $faker->word,
        // 'order'      => $faker->randomDigitNotNull,
        // 'created_at' => $faker->date('Y-m-d H:i:s'),
        // 'updated_at' => $faker->date('Y-m-d H:i:s'),
    ];
});
