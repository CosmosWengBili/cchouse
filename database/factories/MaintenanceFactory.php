<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use App\Model;
use Faker\Generator as Faker;

$factory->define(\App\Maintenance::class, function (Faker $faker) {
    return [
        'commissioner_id' => factory(\App\User::class),
        'maintenance_staff_id' => factory(\App\User::class),
        // 'tenant_contract_id' => factory(\App\TenantContract::class),
        'room_id' => factory(\App\Room::class),
        'afford_by'=>'房東',
        'closed_date' => \Carbon\Carbon::create('next wednesday'),
        'service_comment' => $faker->sentence(),
        'comment' => $faker->sentence(),
        'incident_details' => $faker->sentence(),
        'incident_type' => $faker->randomElement(config('enums.maintenance.incident_type')),
        'work_type' => $faker->randomElement(config('enums.maintenance.work_type')),
    ];
});
