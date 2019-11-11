<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use App\Deposit;
use Faker\Generator as Faker;

$factory->define(Deposit::class, function (Faker $faker) {
    $is_deposit_collected = $faker->boolean;

    $deposit_collection_date = null;
    $deposit_confiscated_amount = null;

    if ($is_deposit_collected) {
        $deposit_collection_date = $faker->dateTimeBetween('-15 day', 'now');
        $deposit_confiscated_amount = $faker->randomDigitNotNull;
    }

    // tenant
    $tenant_contract = \App\TenantContract::inRandomOrder()->first();
    $tenant = $tenant_contract->tenant;

    return [
        'tenant_contract_id'               => $tenant_contract,
        'room_id'                          => $tenant_contract->room_id ?? 0,
        'deposit_collection_date'          => $deposit_collection_date,
        'deposit_collection_serial_number' => $faker->iban(),
        'deposit_confiscated_amount'       => $deposit_confiscated_amount,
        'deposit_returned_amount'          => null,
        'confiscated_or_returned_date'     => null,
        'invoicing_amount'                 => $faker->randomDigitNotNull,
        'invoice_date'                     => $faker->dateTimeBetween('now', '10 day'),
        'is_deposit_collected'             => $is_deposit_collected,
        'comment'                          => $faker->text,
        'payer_name'                       => $tenant->name,
        'payer_certification_number'       => $tenant->certificate_number,
        'payer_is_legal_person'            => $tenant->is_legal_person,
        'payer_phone'                      => $faker->phoneNumber,
        'receiver'                         => \App\User::inRandomOrder()->first(),
        'appointment_date'                 => $faker->date('Y-m-d H:i:s'),
        'reason_of_deletions'              => $faker->word,
        'returned_method'                  => $faker->randomElement(config('enums.deposits.returned_method')),
        'returned_serial_number'           => $faker->iban(),
        'returned_bank'                    => $faker->word,
        'company_allocation_amount'        => $faker->randomDigitNotNull,
        'created_at'                       => $faker->date('Y-m-d H:i:s'),
        'updated_at'                       => $faker->date('Y-m-d H:i:s'),
    ];
});

$factory->state(Deposit::class, 'new', function ($faker) {
    return [
        'room_id'            => factory(\App\Room::class)->states('new'),
        'tenant_contract_id' => factory(\App\TenantContract::class)->states('new'),
    ];
});
