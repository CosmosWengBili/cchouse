<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use App\Audit;
use Faker\Generator as Faker;

$factory->define(Audit::class, function (Faker $faker) {
    return [
        // 'user_type'      => \App\User::class,
        // 'user_id'        => \App\User::inRandomOrder()->first(),
        // 'event'          => $faker->word,
        // 'old_values'     => $faker->text,
        // 'new_values'     => $faker->text,
        // 'url'            => $faker->text,
        // 'ip_address'     => $faker->word,
        // 'user_agent'     => $faker->word,
        // 'tags'           => $faker->word,
        // 'created_at'     => $faker->date('Y-m-d H:i:s'),
        // 'updated_at'     => $faker->date('Y-m-d H:i:s')
    ];
});
