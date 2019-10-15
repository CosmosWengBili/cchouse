<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOverdueFineColumnToTenantContractTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tenant_contract', function (Blueprint $table) {
            $table->unsignedInteger('overdue_fine')->default(0)->comment('滯納金');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tenant_contract', function (Blueprint $table) {
            $table->dropColumn('overdue_fine');
        });
    }
}
