<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterCompanyIncomes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('company_incomes', function (Blueprint $table) {
            $table->unsignedBigInteger('incomable_id');
            $table->string('incomable_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('company_incomes', function (Blueprint $table) {
            $table->dropColumn('incomable_id');
            $table->dropColumn('incomable_type');
        });
    }
}
