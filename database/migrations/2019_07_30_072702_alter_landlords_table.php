<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterLandlordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('landlords', function (Blueprint $table) {
            $table->dropColumn('phone');
            $table->dropColumn('mailing_address');
            $table->dropColumn('email');
            $table->dropColumn('fax_number');
            $table->dropColumn('agent_name');
            $table->dropColumn('agent_certificate_number');
            $table->dropColumn('agent_phone');
            $table->dropColumn('agent_residence_address');
            $table->dropColumn('agent_mailing_address');
            $table->dropColumn('agent_email');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('landlords', function (Blueprint $table) {
            $table->string('phone')->comment('聯絡電話');
            $table->string('mailing_address')->comment('聯絡地址');
            $table->string('email')->comment('電子郵件');
            $table->string('fax_number')->comment('傳真');
            $table->string('agent_name')->comment('代理人姓名');
            $table->string('agent_certificate_number')->comment('代理人證號');
            $table->string('agent_phone')->comment('代理人聯絡電話');
            $table->string('agent_residence_address')->comment('代理人戶籍地址');
            $table->string('agent_mailing_address')->comment('代理人聯絡地址');
            $table->string('agent_email')->comment('代理人電子郵件');
        });
    }
}
