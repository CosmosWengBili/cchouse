<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class SystemVariableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = Carbon::now();

        $data = collect(config('finance.reversal'))->map(function($v, $i) use ($now) {
            return [
                'code'       => $v,
                'value'      => $v,
                'group'      => 'Reversal',
                'order'      => $i,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        })->all();

        DB::table('system_variables')->insert($data);

        DB::table('system_variables')->insert([
            'code'  => 'debt_collection_delay_days',
            'value' => config('finance.debt_collection_delay_days'),
        ]);

        DB::table('system_variables')->insert([
            'code'  => 'default_records_in_index_blade',
            'value' => config('finance.view.default_records_in_index_blade', 200),
        ]);
        DB::table('system_variables')->insert([
            'group' => 'Maintenance',
            'code'  => 'MaintenanceNotifyRequiredDays',
            'value' => 10,
        ]);
        DB::table('system_variables')->insert([
            'group' => 'Management',
            'code'  => 'deposit_rate',
            'value' => 0.00087,
        ]);
    }
}
