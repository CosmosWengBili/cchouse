<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Room;
use Faker\Generator as Faker;

$factory->define(Room::class, function (Faker $faker) {
    $faker->addProvider(new \Faker\Provider\en_US\Payment($faker));

    $management_fee_mode = $faker->randomElement(array_keys(config('enums.rooms.management_fee_mode')));

    return [
        'building_id'                 => \App\Building::inRandomOrder()->first(),
        'room_code'                   => '',
        'electricity_virtual_account' => $faker->bankAccountNumber,
        'virtual_account'             => $faker->bankAccountNumber,
        'room_status'                 => '未出租',
        'room_number'                 => $faker->numberBetween(1, 100),
        'room_layout'                 => '雅房',
        'living_room_count'           => $faker->randomDigitNotNull,
        'room_count'                  => $faker->randomDigitNotNull,
        'bathroom_count'              => $faker->randomDigitNotNull,
        'parking_count'               => $faker->randomDigitNotNull,
        'rent_reserve_price'          => $faker->randomDigitNotNull,
        'rent_actual'                 => $faker->numberBetween(5000, 10000),
        'internet_form'               => $faker->word,
        'management_fee_mode'         => $management_fee_mode,
        'management_fee'              => $management_fee_mode === '比例' ? $faker->randomFloat(2, 10) : $faker->numberBetween(100, 500),
        'wifi_account'                => $faker->word,
        'wifi_password'               => $faker->word,
        'has_digital_tv'              => $faker->word,
        'current_110v'                => $faker->randomDigitNotNull,
        'current_220v'                => $faker->randomDigitNotNull,
        'comment'                     => $faker->text,
        'created_at'                  => $faker->date('Y-m-d H:i:s'),
        'updated_at'                  => $faker->date('Y-m-d H:i:s'),
    ];
});

$factory->state(Room::class, 'new', function ($faker) {
    return [
        'building_id' => factory(\App\Building::class),
    ];
});
