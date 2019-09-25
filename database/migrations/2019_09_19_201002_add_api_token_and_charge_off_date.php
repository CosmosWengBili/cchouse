<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddApiTokenAndChargeOffDate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('api_token')->nullable();
        });
        Schema::table('tenant_electricity_payments', function (Blueprint $table) {
            $table->date('charge_off_date')->nullable()->comment('沖銷日期');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropCloumn('api_token');
        });
        Schema::table('tenant_electricity_payments', function (Blueprint $table) {
            $table->dropCloumn('charge_off_date');
        });
    }
}
