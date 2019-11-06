<?php

use Illuminate\Database\Seeder;

use Carbon\Carbon;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = App\User::create([
            'name'              => 'TestUser',
            'email'             => 'tt@tt.tt',
            'email_verified_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'password'          => Hash::make('tttttt'),
        ]);

        // roles
        $user->assignRole('管理組');
        // foreach (\App\Group::all() as $group) {
        // }
    }
}
