<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeLandlordContractColumnsNullable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('landlord_contracts', function (Blueprint $table) {
            $table->integer('annual_service_fee_month_count')->nullable()->comment('年繳服務費月數')->change();
            $table->string('agency_service_fee')->nullable()->comment('仲介服務費')->change();
            $table->float('adjust_ratio')->nullable()->comment('調整%數')->change();
            $table->float('withdrawal_revenue_distribution')->default(0.5)->comment('盈餘分配')->change();
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
            $table->integer('annual_service_fee_month_count')->comment('年繳服務費月數')->change();
            $table->string('agency_service_fee')->comment('仲介服務費')->change();
            $table->float('adjust_ratio')->comment('調整%數')->change();
            $table->float('withdrawal_revenue_distribution')->comment('盈餘分配')->change();
        });
    }
}
