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

        $this->call(UsersTableSeeder::class);
        $this->command->info('users table seeded!');

        $this->call(SystemVariableSeeder::class);
        $this->command->info('system variable table seeded!');

        // test
        $this->call(FakeDataSeeder::class);
        $this->command->info('test date seeded!');
    }
}
