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
    }
}
