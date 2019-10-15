<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddWithdrawalRevenueDistributionColumnToLandlordContractsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('landlord_contracts', function (Blueprint $table) {
            $table->float('withdrawal_revenue_distribution')->default(0)->comment('盈餘分配');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('landlord_contracts', function (Blueprint $table) {
            $table->dropColumn('withdrawal_revenue_distribution');
        });
    }
}
