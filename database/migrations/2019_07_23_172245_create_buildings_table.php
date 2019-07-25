<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBuildingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('buildings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title')->comment('簡稱');
            $table->string('city')->comment('縣市');
            $table->string('district')->comment('區域');
            $table->string('address')->comment('地址');
            $table->string('tax_number')->comment('稅籍編號');
            $table->string('building_type')->comment('物件類型');
            $table->integer('floor')->comment('樓層');
            $table->string('legal_usage')->comment('法定用途');
            $table->boolean('has_elevator')->comment('電梯');
            $table->string('security_guard')->comment('管理室和管理員');
            $table->string('management_count')->comment('管理件數');
            $table->string('first_floor_door_opening')->comment('一樓大門開門方式');
            $table->string('public_area_door_opening')->comment('各樓層公區開門方式');
            $table->string('room_door_opening')->comment('臥室門開門方式');
            $table->string('main_ammeter_location')->comment('台電總電表位址');
            $table->string('ammeter_serial_number_1')->comment('台電電號1( 可能多個 )');
            $table->string('shared_electricity')->comment('公電');
            $table->string('electricity_payment_method')->comment('台電帳單付款方式');
            $table->string('private_ammeter_location')->comment('自設分電表位置');
            $table->string('water_meter_location')->comment('自來水表位置');
            $table->string('water_meter_serial_number')->comment('自來水表表號');
            $table->string('water_payment_method')->comment('自來水帳單付款方式');
            $table->date('water_meter_reading_date')->nullable()->comment('水錶抄表日期');
            $table->string('gas_meter_location')->comment('天然氣表位置');
            $table->string('garbage_collection_location')->comment('收垃圾地點');
            $table->string('garbage_collection_time')->comment('收垃圾時間');
            $table->string('management_fee_payment_method')->comment('管理費繳費方式');
            $table->string('management_fee_contact')->comment('管理費聯絡人');
            $table->string('management_fee_contact_phone')->comment('管理費聯絡電話');
            $table->string('distribution_method')->comment('分配方式');
            $table->string('administrative_number')->comment('行政區碼');
            $table->string('accounting_group')->comment('會計組別');
            $table->string('rental_receipt')->comment('租金收據');
            $table->unsignedBigInteger('commissioner_id')->nullable()->comment('招租人員');
            $table->unsignedBigInteger('administrator_id')->nullable()->comment('管理人員');
            $table->text('comment');

            $table->timestamps();
            $table->softDeletes();


            $table->foreign('commissioner_id')
                ->references('id')->on('users');

            $table->foreign('administrator_id')
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
        Schema::dropIfExists('buildings');
    }
}
