<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AugustUpdated extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('landlord_contract', function (Blueprint $table) {
            $table->string('agency_service_fee')->after('taxable_charter_fee')->comment('仲介服務費');
        });
        Schema::table('tenant_payments', function (Blueprint $table) {
            $table->boolean('is_pay_off')->after('is_visible_at_report')->comment('是否為點交');
        });
        Schema::table('tenant_contract', function (Blueprint $table) {
            $table->dropColumn('commissioner_rate');
        });
        Schema::table('landlord_payments', function (Blueprint $table) {
            $table->dropForeign(['building_id']);
            $table->dropColumn('building_id');
            $table->unsignedBigInteger('room_id')->comment('房 ID');

            $table->foreign('room_id')
                ->references('id')
                ->on('rooms');
        });
        Schema::table('landlord_other_subjects', function (Blueprint $table) {
            $table->dropForeign(['building_id']);
            $table->dropColumn('building_id');
            $table->unsignedBigInteger('room_id')->comment('房 ID');
            $table->foreign('room_id')
                ->references('id')
                ->on('rooms');
        });
        Schema::table('buildings', function (Blueprint $table) {
            $table->dropForeign(['landlord_contract_id']);
            $table->dropColumn('landlord_contract_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('landlord_contract', function (Blueprint $table) {
            $table->dropColumn('agency_service_fee');
        });
        Schema::table('tenant_payments', function (Blueprint $table) {
            $table->dropColumn('is_pay_off');
        });
        Schema::table('tenant_contract', function (Blueprint $table) {
            $table->float('commissioner_rate')->comment('專員服務費費率');
        });
        Schema::table('landlord_payments', function (Blueprint $table) {
            $table->dropForeign(['room_id']);
            $table->dropColumn('room_id');
            $table->unsignedBigInteger('building_id')->comment('物件 ID');
            $table->foreign('building_id') 
                ->references('id')
                ->on('buildings');
        });
        Schema::table('landlord_other_subjects', function (Blueprint $table) {
            $table->dropForeign(['room_id']);
            $table->dropColumn('room_id');
            $table->unsignedBigInteger('building_id')->comment('物件 ID');
            $table->foreign('building_id') 
                ->references('id')
                ->on('buildings');
        });
        Schema::table('buildings', function (Blueprint $table) {
            $table->unsignedBigInteger('landlord_contract_id')->comment('物件 ID');
            $table->foreign('landlord_contract_id')
                ->references('id')
                ->on('landlord_contract');
        });
    }
}
