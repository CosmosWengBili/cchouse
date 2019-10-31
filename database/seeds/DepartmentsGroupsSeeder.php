<?php

use Illuminate\Database\Seeder;
use App\Traits\Database\Seeder\UpdateOrCreate;
use App\Permission;
use App\Group;
use App\Department;

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
            ['id' => 1, 'name' => '開發 development'],
            ['id' => 2, 'name' => '管理 management'],
            ['id' => 3, 'name' => '帳務 accounting'],
        ];
        $groups = [
            [
                'id' => 1, 'name' => '管理組', 'department_id' => 2, 'guard_name' => 'web',
                'permissions' => ['delete building']
            ],
            ['id' => 2, 'name' => '帳務組', 'department_id' => 3, 'guard_name' => 'web'],
            ['id' => 3, 'name' => '開發組', 'department_id' => 1, 'guard_name' => 'web'],
        ];

        foreach ($departments as $department) {
            $department = Department::updateOrCreate($department);
        }

        foreach ($groups as $group) {
            $permissions = $group['permissions']?? [];
            unset($group['permissions']);

            $group = Group::updateOrCreate($group);

            foreach ($permissions as $permission) {
                $permission = Permission::updateOrCreate(['name' => $permission]);
                $permission->assignRole($group);
            }
        }
    }
}
