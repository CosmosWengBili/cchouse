<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRoomIdToMaintenancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();
        Schema::table('maintenances', function (Blueprint $table) {
            $table->unsignedBigInteger('room_id')->after('tenant_contract_id')->comment('室ID');
            $table->foreign('room_id')->references('id')->on('rooms');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::disableForeignKeyConstraints();
        Schema::table('maintenances', function (Blueprint $table) {
            $table->dropForeign(['room_id']);
            $table->dropColumn('room_id');
        });
    }
}
