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
            ['id' => 1, 'name' => '帳務處'],
            ['id' => 2, 'name' => '管理處'],
        ];
        $groups = [
            ['id' => 1, 'name' => '帳務組', 'department_id' => 1, 'guard_name' => 'web'],
            ['id' => 2, 'name' => '管理組', 'department_id' => 2, 'guard_name' => 'web'],
        ];

        foreach ($departments as $department) {
            $this->updateOrCreate(\App\Department::class, $department);
        }
        foreach ($groups as $group) {
            $this->updateOrCreate(\App\Group::class, $group);
        }
    }
}
