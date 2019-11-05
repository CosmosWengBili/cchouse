<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDecorationPriceToBuildingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('buildings', function (Blueprint $table) {
            $table->boolean('decoration_needed')->default(0)->comment('是否需裝修')->after('squatter_status');
            $table->integer('decoration_price')->nullable()->comment('裝修價格')->after('decoration_needed');
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
            $table->dropColumn('decoration_needed');
            $table->dropColumn('decoration_price');
        });
    }
}
