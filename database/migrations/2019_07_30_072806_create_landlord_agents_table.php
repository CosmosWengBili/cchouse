<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLandlordAgentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('landlord_agents', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('landlord_id')->comment('房東 ID');
            $table->string('name')->comment('代理人姓名');
            $table->string('certificate_number')->comment('代理人證號');
            $table->string('phone')->comment('代理人聯絡電話');
            $table->string('residence_address')->comment('代理人戶籍地址');
            $table->string('mailing_address')->comment('代理人聯絡地址');
            $table->string('email')->comment('代理人電子郵件');
            
            $table->timestamps();
            $table->softDeletes();


            $table->foreign('landlord_id')
                ->references('id')->on('landlords');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('landlord_agents');
    }
}
