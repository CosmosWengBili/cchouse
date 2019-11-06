<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterColumnsInRoomsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->dropColumn('room_attribute');
            $table->dropColumn('ammeter_reading_date');
            $table->dropColumn('rent_list_price');
            $table->dropColumn('rent_landlord');

            $table->dropColumn('needs_decoration');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->string('room_attribute')->comment('物件屬性')->after('room_layout');
            $table->date('ammeter_reading_date')->nullable()->comment('電表抄表日期')->after('electricity_virtual_account');
            $table->integer('rent_list_price')->comment('租金牌價')->after('rent_actual');
            $table->integer('rent_landlord')->comment('房東租金')->after('rent_list_price');

            $table->boolean('needs_decoration')->comment('是否需裝修')->after('room_layout');
        });
    }
}
