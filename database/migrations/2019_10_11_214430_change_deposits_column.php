<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeDepositsColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('deposits', function (Blueprint $table) {
            $table->unsignedBigInteger('tenant_contract_id')->nullable()->comment('租客合約 ID')->change();
            $table->unsignedBigInteger('room_id')->comment('房 ID');
        });

        DB::update('
            UPDATE 
                deposits LEFT JOIN tenant_contract ON tenant_contract.id = deposits.tenant_contract_id
            SET deposits.room_id = tenant_contract.room_id;
        ');

        Schema::table('deposits', function (Blueprint $table) {
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
        Schema::table('deposits', function (Blueprint $table) {
            $table->dropForeign('deposits_room_id_foreign');
            $table->dropColumn('room_id');
            $table->unsignedBigInteger('tenant_contract_id')->comment('租客合約 ID')->change();
        });
    }
}
