<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsToLandlord extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('landlords', function (Blueprint $table) {
            $table->string('birth')->comment('出生年月日');
            $table->text('note')->comment('備註');
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
            $table->dropColumn('birth');
            $table->dropColumn('note');
        });
    }
}
