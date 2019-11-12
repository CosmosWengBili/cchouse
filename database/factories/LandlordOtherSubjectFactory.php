<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use App\LandlordOtherSubject;
use Faker\Generator as Faker;

$factory->define(LandlordOtherSubject::class, function (Faker $faker) {
    return [
        'subject' => $faker->randomElement(config('enums.tenant_payments.subject')),
        'subject_type' => $faker->word,
        'income_or_expense' => $faker->randomElement(config('enums.landlord_other_subjects.income_or_expense')),
        'expense_date' => $faker->dateTimeBetween('-15 day', '15 day'),
        'amount' => $faker->randomDigitNotNull,
        'comment' => $faker->text,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'updated_at' => $faker->date('Y-m-d H:i:s'),
        'room_id' => \App\Room::inRandomOrder()->first(),
        'is_invoiced' => $faker->boolean,
        'invoice_item_name' => $faker->word
    ];
});
