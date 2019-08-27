<?php

use Illuminate\Database\Seeder;
use App\Traits\Database\Seeder\UpdateOrCreate;

class SystemVariablesSeeder extends Seeder
{
    use UpdateOrCreate;
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $system_variables = [
            ['id' => 1, 'code' => '押金設算息', 'value' => '0.00087'],
        ];

        foreach ($system_variables as $system_variable) {
            $this->updateOrCreate(\App\SystemVariable::class, $system_variable);
        }

    }
}
