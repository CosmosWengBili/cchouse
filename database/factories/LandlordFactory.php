<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use App\Landlord;
use Faker\Generator as Faker;

$factory->define(Landlord::class, function (Faker $faker) {
    $faker->addProvider(new \Faker\Provider\it_IT\Person($faker));

    return [
        'name'                        => $faker->name(),
        'certificate_number'          => $faker->taxId(),
        'is_legal_person'             => $faker->boolean,
        'residence_address'           => $faker->word,
        'is_collected_by_third_party' => $faker->boolean,
        'birth'                       => $faker->date('Y-m-d H:i:s'),
        'note'                        => $faker->text,
        'bank_code'                   => $faker->randomDigitNotNull,
        'branch_code'                 => $faker->word,
        'account_name'                => $faker->word,
        'account_number'              => $faker->word,
        'invoice_collection_method'   => $faker->word,
        'invoice_collection_number'   => $faker->word,
        'invoice_mailing_address'     => $faker->word,
        'created_at'                  => $faker->date('Y-m-d H:i:s'),
        'updated_at'                  => $faker->date('Y-m-d H:i:s'),
    ];
});
