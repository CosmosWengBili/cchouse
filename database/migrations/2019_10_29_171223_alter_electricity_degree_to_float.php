<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterElectricityDegreeToFloat extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pay_offs', function (Blueprint $table) {
            $table->float('110v_degree')->change();
            $table->float('220v_degree')->change();
        });
        Schema::table('rooms', function (Blueprint $table) {
            $table->float('current_110v')->change();
            $table->float('current_220v')->change();
        });
        Schema::table('tenant_contract', function (Blueprint $table) {
            $table->float('110v_start_degree')->change();
            $table->float('220v_start_degree')->change();
            $table->float('110v_end_degree')->change();
            $table->float('220v_end_degree')->change();
        });
        Schema::table('tenant_electricity_payments', function (Blueprint $table) {
            $table->float('110v_start_degree')->change();
            $table->float('220v_start_degree')->change();
            $table->float('110v_end_degree')->change();
            $table->float('220v_end_degree')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pay_offs', function (Blueprint $table) {
            $table->integer('110v_degree')->change();
            $table->integer('220v_degree')->change();
        });
        Schema::table('rooms', function (Blueprint $table) {
            $table->integer('current_110v')->change();
            $table->integer('current_220v')->change();
        });
        Schema::table('tenant_contract', function (Blueprint $table) {
            $table->integer('110v_start_degree')->change();
            $table->integer('220v_start_degree')->change();
            $table->integer('110v_end_degree')->change();
            $table->integer('220v_end_degree')->change();
        });
        Schema::table('tenant_electricity_payments', function (Blueprint $table) {
            $table->integer('110v_start_degree')->change();
            $table->integer('220v_start_degree')->change();
            $table->integer('110v_end_degree')->change();
            $table->integer('220v_end_degree')->change();
        });
    }
}
