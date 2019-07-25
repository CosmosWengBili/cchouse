<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRoomsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rooms', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('building_id')->comment('物件 ID');
            $table->boolean('needs_decoration')->comment('是否需裝修');
            $table->string('room_code')->comment('物件代碼');
            $table->string('virtual_account')->comment('虛擬帳號');
            $table->string('room_status')->comment('狀態');
            $table->string('room_number')->comment('房號');
            $table->string('room_layout')->comment('物件格局');
            $table->string('room_attribute')->comment('物件屬性');
            $table->integer('living_room_count')->comment('客餐廳');
            $table->integer('room_count')->comment('房間');
            $table->integer('bathroom_count')->comment('衛浴');
            $table->integer('parking_count')->comment('車位');
            $table->date('ammeter_reading_date')->comment('電表抄表日期');
            $table->integer('rent_list_price')->comment('租金牌價');
            $table->integer('rent_reserve_price')->comment('租金底價');
            $table->integer('rent_landlord')->comment('房東租金');
            $table->integer('rent_actual')->comment('實際租金');
            $table->string('internet_form')->comment('網路形式');
            $table->string('management_fee_mode')->comment('管理費模式');
            $table->float('management_fee')->comment('管理費');
            $table->string('wifi_account')->comment('Wifi 帳號');
            $table->string('wifi_password')->comment('Wifi 密碼');
            $table->boolean('has_digital_tv')->comment('數位電視');
            $table->boolean('can_keep_pets')->comment('養寵物');
            $table->string('gender_limit')->comment('性別限制');
            $table->text('comment');
            
            $table->timestamps();
            $table->softDeletes();

                
            $table->foreign('building_id')
                ->references('id')->on('buildings');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rooms');
    }
}
