<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Tenant;
use Faker\Generator as Faker;

$factory->define(Tenant::class, function (Faker $faker) {

    return [
        'name' => $faker->word,
        'certificate_number' => $faker->word,
        'is_legal_person' => $faker->word,
        'line_id' => $faker->word,
        'residence_address' => $faker->word,
        'company' => $faker->word,
        'job_position' => $faker->word,
        'company_address' => $faker->word,
        'confirm_by' => $faker->word,
        'confirm_at' => $faker->word,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'updated_at' => $faker->date('Y-m-d H:i:s'),
        'deleted_at' => $faker->date('Y-m-d H:i:s'),
        'birth' => $faker->word
    ];
});
