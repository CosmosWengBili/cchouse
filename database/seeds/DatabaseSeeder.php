<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(DepartmentsGroupsSeeder::class);
        $this->command->info('departments and groups table seeded!');
        $this->call(SystemVariablesSeeder::class);
        $this->command->info('generate init system variables!');
    }
}
