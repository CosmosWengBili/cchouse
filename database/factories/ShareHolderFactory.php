<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use App\Shareholder;
use Faker\Generator as Faker;

$factory->define(Shareholder::class, function (Faker $faker) {
    $faker->addProvider(new \Faker\Provider\en_US\Payment($faker));
    $faker->addProvider(new \Faker\Provider\ms_MY\Payment($faker));

    $name = $faker->name;

    return [
        'name'                        => $name,
        'contact_method'              => $faker->email,
        'bank_name'                   => $faker->bank,
        'bank_code'                   => $faker->word,
        'account_number'              => $faker->bankAccountNumber,
        'account_name'                => $name,
        'is_remittance_fee_collected' => $faker->boolean,
        'transfer_from'               => $faker->randomElement(array_keys(config('enums.shareholders.transfer_from'))),
        'bill_delivery'               => $faker->word,
        'distribution_method'         => $faker->randomElement(array_keys(config('enums.shareholders.distribution_method'))),
        'distribution_start_date'     => $faker->dateTimeBetween('-3 years', 'now'),
        'distribution_end_date'       => $faker->dateTimeBetween('1 years', '3 years'),
        'distribution_rate'           => $faker->randomFloat,
        'distribution_amount'         => $faker->randomDigitNotNull,
        'investment_amount'           => $faker->randomDigitNotNull,
        'exchange_fee'                => $faker->randomDigitNotNull,
        'bank_branch'                 => $faker->randomElement(array_keys(config('enums.shareholders.transfer_from'))),
        'method'                      => $faker->word,
        'created_at'                  => $faker->date('Y-m-d H:i:s'),
        'updated_at'                  => $faker->date('Y-m-d H:i:s'),
    ];
});
