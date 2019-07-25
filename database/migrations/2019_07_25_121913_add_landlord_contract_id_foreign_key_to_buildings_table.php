<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLandlordContractIdForeignKeyToBuildingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('buildings', function (Blueprint $table) {
            $table->unsignedBigInteger('landlord_contract_id')->comment('房東合約 ID');

            $table->foreign('landlord_contract_id')
                ->references('id')->on('landlord_contract');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('buildings', function (Blueprint $table) {
            if(Schema::hasColumn('buildings', 'landlord_contract_id')) {
                $table->dropColumn('landlord_contract_id');
            }
        });
    }
}
