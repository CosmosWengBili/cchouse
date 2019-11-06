<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use App\Tenant;
use Faker\Generator as Faker;

$factory->define(Tenant::class, function (Faker $faker) {
    $faker->addProvider(new \Faker\Provider\es_ES\Person($faker));

    return [
        'name'               => $faker->word,
        'certificate_number' => $faker->vat(false),
        'is_legal_person'    => $faker->boolean,
        'line_id'            => $faker->word,
        'residence_address'  => $faker->address,
        'company'            => $faker->company,
        'job_position'       => $faker->jobTitle,
        'company_address'    => $faker->address,
        'confirm_by'         => \App\User::inRandomOrder()->first(),
        'confirm_at'         => $faker->date('Y-m-d H:i:s'),
        'created_at'         => $faker->date('Y-m-d H:i:s'),
        'updated_at'         => $faker->date('Y-m-d H:i:s'),
        'birth'              => $faker->date('Y-m-d H:i:s'),
    ];
});

$factory->state(Tenant::class, 'new', function ($faker) {
    return [
        'confirm_by' => factory(\App\User::class),
    ];
});
