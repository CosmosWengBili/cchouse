<?php

use Illuminate\Database\Seeder;
use App\User;
use App\Group;
use App\Permission;

class testAddGroupMappingForUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = User::all();

        // $group = Group::create(['name' => '管理組']);

        foreach ($users as $user) {
            $user->assignRole('管理組');
        }
    }
}
