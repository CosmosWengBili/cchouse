<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterDebtCollectionsTableAllowNullOnCollectorId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('debt_collections', function (Blueprint $table) {
            $table->unsignedBigInteger('collector_id')->comment('催收人')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('debt_collections', function (Blueprint $table) {
            $table->unsignedBigInteger('collector_id')->comment('催收人')->nullable(false)->change();
        });
    }
}
