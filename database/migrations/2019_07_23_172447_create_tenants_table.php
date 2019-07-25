<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTenantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tenants', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->comment('姓名');
            $table->string('certificate_number')->comment('證號');
            $table->boolean('is_legal_person')->comment('是否為法人');
            $table->string('phone')->comment('電話');
            $table->string('email')->comment('Email');
            $table->string('line_id')->comment('LineID');
            $table->string('residence_address')->comment('戶籍地址');
            $table->string('mailing_address')->comment('通訊地址');
            $table->string('company')->comment('任職公司');
            $table->string('job_position')->comment('任職職位');
            $table->string('company_address')->comment('任職公司地址');
            $table->string('family_emergency_contact')->comment('親屬緊急聯絡人');
            $table->string('relationship_to_family_emergency_contact')->comment('親屬緊急聯絡人關係');
            $table->string('family_emergency_contact_phone')->comment('親屬緊急聯絡人電話');
            $table->string('guarantor')->comment('保證人');
            $table->string('relationship_to_guarantor')->comment('保證人關係');
            $table->string('guarantor_phone')->comment('保證人電話');
            $table->string('friend_emergency_contact')->comment('朋友緊急聯絡人');
            $table->string('relationship_to_friend_emergency_contact')->comment('朋友緊急聯絡人關係');
            $table->string('friend_emergency_contact_phone')->comment('朋友緊急聯絡人電話');
            $table->unsignedBigInteger('confirm_by')->comment('資料確認人員');
            $table->date('confirm_at')->comment('資料確認日');

            $table->timestamps();
            $table->softDeletes();


            $table->foreign('confirm_by')
                ->references('id')->on('users');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tenants');
    }
}
