<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MakeBuildingColumnNullable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('buildings', function (Blueprint $table) {
            $table->string('security_guard')->nullable()->comment('管理室和管理員')->change();
            $table->string('first_floor_door_opening')->nullable()->comment('一樓大門開門方式')->change();
            $table->string('public_area_door_opening')->nullable()->comment('各樓層公區開門方式')->change();
            $table->string('room_door_opening')->nullable()->comment('臥室門開門方式')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('buildings', function (Blueprint $table) {
            $table->string('security_guard')->comment('管理室和管理員')->change();
            $table->string('first_floor_door_opening')->comment('一樓大門開門方式')->change();
            $table->string('public_area_door_opening')->comment('各樓層公區開門方式')->change();
            $table->string('room_door_opening')->comment('臥室門開門方式')->change();
        });
    }
}
