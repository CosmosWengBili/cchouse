<?php

namespace App\Services;

use App\Deposit;
use App\CompanyIncome;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DepositService
{
   public static function update(Deposit $deposit, $newValues) {

        DB::transaction(function () use ($deposit, $newValues) {
            if (isset($newValues['confiscated_or_returned_date']) && isset($newValues['deposit_confiscated_amount']) && $newValues['deposit_confiscated_amount'] != 0 ) {
                // make new company income
                CompanyIncome::create([
                    'incomable_type' => Deposit::class,
                    'incomable_id' => $deposit->id,
                    'subject' => '訂金',
                    'income_date' => Carbon::today(),
                    'amount' => $newValues['deposit_confiscated_amount'],
                ]);
            }
            $deposit->update($newValues);
        });
   }
}
