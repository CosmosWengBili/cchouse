<?php

use Illuminate\Database\Seeder;
use Illuminate\Http\Request;

class testMaintenanceMarkDoneSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = App\User::group('帳務組')->inRandomOrder()->first();
        if (! $user) {
            $this->command->info('there is no user in the group');

            return ;
        }

        $maintenanceIds = array_map(
            function ($maintenance) {
                return $maintenance['id'];
            },
            factory(\App\Maintenance::class, rand(6, 12))->create([
                'status' => '請款中',
                'afford_by' => '房東'
            ])->toArray()
        );

        $request = new Request;
        $request->setUserResolver(function () use ($user) {
            return $user;
        });
        $request->merge([
            'who' => 'landlord',
            'maintenanceIds' => $maintenanceIds,
        ]);

        // 從 maintenances 產生  landlord payment
        $MaintenanceController = new \App\Http\Controllers\MaintenanceController;
        $result = $MaintenanceController->markDone($request);

        $this->command->info('MaintenanceController markDone: ');
        $this->command->info($result);
    }
}
