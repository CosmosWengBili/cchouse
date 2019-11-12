<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use App\Maintenance;
use Faker\Generator as Faker;

$factory->define(Maintenance::class, function (Faker $faker) {
    return [
        'room_id'                       => \App\Room::inRandomOrder()->first(),
        'reported_at'                   => \Carbon\Carbon::now(),
        'expected_service_date'         => \Carbon\Carbon::tomorrow(),
        'expected_service_time'         => $faker->time(),
        'dispatch_date'                 => \Carbon\Carbon::tomorrow(),
        'commissioner_id'               => \App\User::inRandomOrder()->first(),
        'maintenance_staff_id'          => \App\User::inRandomOrder()->first(),
        'closed_date'                   => \Carbon\Carbon::create('next wednesday'),
        'closed_comment'                => $faker->text,
        'service_comment'               => $faker->sentence(),
        'status'                        => $faker->randomElement(config('enums.maintenance.status')),
        'incident_details'              => $faker->sentence(),
        'incident_type'                 => $faker->randomElement(config('enums.maintenance.incident_type')),
        'work_type'                     => $faker->randomElement(config('enums.maintenance.work_type')),
        'number_of_times'               => $faker->randomDigitNotNull,
        'payment_request_date'          => $faker->word,
        'closing_serial_number'         => $faker->word,
        'billing_details'               => $faker->text,
        'payment_request_serial_number' => $faker->word,
        'cost'                          => $faker->randomDigitNotNull,
        'price'                         => $faker->randomDigitNotNull,
        'afford_by'                     => $faker->randomElement(config('enums.tenant_payments.collected_by')),
        'is_recorded'                   => $faker->boolean,
        'comment'                       => $faker->sentence(),
        'created_at'                    => $faker->date('Y-m-d H:i:s'),
        'updated_at'                    => $faker->dateTimeBetween('-15 day', '15 day'),
        'is_printed'                    => $faker->boolean
    ];
});

$factory->state(Maintenance::class, 'new', function ($faker) {
    return [
        'room_id'              => factory(\App\Room::class)->states('new'),
        'commissioner_id'      => factory(\App\User::class),
        'maintenance_staff_id' => factory(\App\User::class),
    ];
});
