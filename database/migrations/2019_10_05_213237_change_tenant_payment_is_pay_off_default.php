<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeTenantPaymentIsPayOffDefault extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tenant_payments', function (Blueprint $table) {
            $table->boolean('is_pay_off')->default(false)->after('is_visible_at_report')->comment('是否為點交')->change();
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
            $table->boolean('is_pay_off')->default(false)->after('is_visible_at_report')->comment('是否為點交')->change();
        });
    }
}
