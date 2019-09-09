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

        // 押金設算息
        $system_variables = [
            ['id' => 1, 'code' => 'deposit_rate', 'value' => '0.00087'],
        ];

        foreach ($system_variables as $system_variable) {
            $this->updateOrCreate(\App\SystemVariable::class, $system_variable);
        }
    }
}
