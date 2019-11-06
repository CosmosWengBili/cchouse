<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use App\Deposit;
use Faker\Generator as Faker;

$factory->define(Deposit::class, function (Faker $faker) {
    return [
        'tenant_contract_id'               => \App\TenantContract::inRandomOrder()->first(),
        'room_id'                          => \App\Room::inRandomOrder()->first(),
        'deposit_collection_date'          => $faker->word,
        'deposit_collection_serial_number' => $faker->word,
        'deposit_confiscated_amount'       => $faker->randomDigitNotNull,
        'deposit_returned_amount'          => $faker->randomDigitNotNull,
        'confiscated_or_returned_date'     => $faker->word,
        'invoicing_amount'                 => $faker->randomDigitNotNull,
        'invoice_date'                     => $faker->word,
        'is_deposit_collected'             => $faker->boolean,
        'comment'                          => $faker->text,
        'payer_name'                       => $faker->word,
        'payer_certification_number'       => $faker->word,
        'payer_is_legal_person'            => $faker->boolean,
        'payer_phone'                      => $faker->phoneNumber,
        'receiver'                         => \App\User::inRandomOrder()->first(),
        'appointment_date'                 => $faker->word,
        'reason_of_deletions'              => $faker->word,
        'returned_method'                  => $faker->word,
        'returned_serial_number'           => $faker->word,
        'returned_bank'                    => $faker->word,
        'company_allocation_amount'        => $faker->randomDigitNotNull,
        'created_at'                       => $faker->date('Y-m-d H:i:s'),
        'updated_at'                       => $faker->date('Y-m-d H:i:s'),
    ];
});
