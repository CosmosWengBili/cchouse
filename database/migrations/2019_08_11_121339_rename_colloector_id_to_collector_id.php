<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameColloectorIdToCollectorId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('debt_collections', function (Blueprint $table) {
            $table->renameColumn('colloector_id', 'collector_id');
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
            $table->renameColumn('collector_id', 'colloector_id');
        });
    }
}
