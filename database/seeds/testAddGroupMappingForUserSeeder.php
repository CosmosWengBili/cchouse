<?php

use Illuminate\Database\Seeder;
use App\User;

class testAddGroupMappingForUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Default
        $user = User::find(1);
        $user->assignGroup('管理組');
        $user->assignGroup('帳務組');

        $users = User::inRandomOrder()->take(rand(1, 100))->get();
        foreach ($users as $user) {
            $user->assignGroup('管理組');
        }

        $users = User::inRandomOrder()->take(rand(1, 100))->get();
        foreach ($users as $user) {
            $user->assignGroup('帳務組');
        }
    }
}
