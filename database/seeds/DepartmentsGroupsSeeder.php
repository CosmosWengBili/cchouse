<?php

use Illuminate\Database\Seeder;
use App\Traits\Database\Seeder\UpdateOrCreate;

class DepartmentsGroupsSeeder extends Seeder
{
    use UpdateOrCreate;
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $departments = [
            ['id' => 1, 'name' => '管理 management'],
            ['id' => 2, 'name' => '管理 management'],
            ['id' => 3, 'name' => '帳務 accounting'],
        ];
        $groups = [
            ['id' => 1, 'name' => '管理組', 'department_id' => 2, 'guard_name' => 'web'],
            ['id' => 2, 'name' => '帳務組', 'department_id' => 3, 'guard_name' => 'web'],
        ];

        foreach ($departments as $department) {
            $this->updateOrCreate(\App\Department::class, $department);
        }
        foreach ($groups as $group) {
            $this->updateOrCreate(\App\Group::class, $group);
        }
    }
}
