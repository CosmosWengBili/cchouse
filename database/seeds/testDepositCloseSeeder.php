<?php

use Illuminate\Database\Seeder;
use Illuminate\Http\Request;
use Faker\Generator as Faker;

class testDepositCloseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(Faker $faker)
    {
        //

        //

        $request = new Request;
        $request->merge([
            'deposit_returned_amount' => null,
            'confiscated_or_returned_date' => \Carbon\Carbon::now(),
            'returned_method' => $faker->randomElement(config('enums.deposits.returned_method')),
            'returned_bank' => null,
            'returned_serial_number' => null,
            'deposit_confiscated_amount' => 7777,
            'company_allocation_amount' => 3333,
        ]);

        $deposit = App\Deposit::inRandomOrder()->first();
        if ($deposit) {
            $DepositController = new \App\Http\Controllers\DepositController;
            $DepositController->close($request, $deposit);
        }

        $this->command->info('DepositController close: ');
    }
}
