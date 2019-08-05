<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveTenantUnusedColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn('phone');
            $table->dropColumn('mailing_address');
            $table->dropColumn('email');

            // 親屬緊急聯絡人
            $table->dropColumn('family_emergency_contact');
            $table->dropColumn('relationship_to_family_emergency_contact');
            $table->dropColumn('family_emergency_contact_phone');

            // 保證人
            $table->dropColumn('guarantor');
            $table->dropColumn('relationship_to_guarantor');
            $table->dropColumn('guarantor_phone');

            // 朋友緊急聯絡人
            $table->dropColumn('friend_emergency_contact');
            $table->dropColumn('relationship_to_friend_emergency_contact');
            $table->dropColumn('friend_emergency_contact_phone');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->string('phone')->comment('電話');
            $table->string('email')->comment('Email');
            $table->string('mailing_address')->comment('通訊地址');

            $table->string('family_emergency_contact')->comment('親屬緊急聯絡人');
            $table->string('relationship_to_family_emergency_contact')->comment('親屬緊急聯絡人關係');
            $table->string('family_emergency_contact_phone')->comment('親屬緊急聯絡人電話');

            $table->string('guarantor')->comment('保證人');
            $table->string('relationship_to_guarantor')->comment('保證人關係');
            $table->string('guarantor_phone')->comment('保證人電話');

            $table->string('friend_emergency_contact')->comment('朋友緊急聯絡人');
            $table->string('relationship_to_friend_emergency_contact')->comment('朋友緊急聯絡人關係');
            $table->string('friend_emergency_contact_phone')->comment('朋友緊急聯絡人電話');
        });
    }
}
