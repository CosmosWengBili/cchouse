<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLandlordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('landlords', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->comment('姓名');
            $table->string('certificate_number')->comment('證號');
            $table->boolean('is_legal_person')->comment('是否為法人');
            $table->string('phone')->comment('聯絡電話');
            $table->string('residence_address')->comment('戶籍地址');
            $table->string('mailing_address')->comment('聯絡地址');
            $table->string('email')->comment('電子郵件');
            $table->string('fax_number')->comment('傳真');
            $table->string('agent_name')->comment('代理人姓名');
            $table->string('agent_certificate_number')->comment('代理人證號');
            $table->string('agent_phone')->comment('代理人聯絡電話');
            $table->string('agent_residence_address')->comment('代理人戶籍地址');
            $table->string('agent_mailing_address')->comment('代理人聯絡地址');
            $table->string('agent_email')->comment('代理人電子郵件');
            $table->boolean('is_collected_by_third_party')->comment('是否第三人代收');
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('landlords');
    }
}
