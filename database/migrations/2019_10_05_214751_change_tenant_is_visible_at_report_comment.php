<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeTenantIsVisibleAtReportComment extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tenant_payments', function (Blueprint $table) {
            $table->boolean('is_visible_at_report')->comment('是否顯示在月結單')->change();
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
            $table->boolean('is_visible_at_report')->comment('是否顯示在報表')->change();
        });
    }
}
