<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeTenantPaymentColumnDefault extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tenant_payments', function (Blueprint $table) {
            $table->text('comment')->default('')->comment('備註')->change();
        });

        Schema::table('tenant_electricity_payments', function (Blueprint $table) {
            $table->text('comment')->default('')->comment('備註')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tenant_payments', function (Blueprint $table) {
            $table->text('comment')->comment('備註')->change();
        });

        Schema::table('tenant_electricity_payments', function (Blueprint $table) {
            $table->text('comment')->comment('備註')->change();
        });

    }
}
