<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAndRemoveColumnsToKeysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('keys', function (Blueprint $table) {
            $table->text('comment')->nullable()->comment('備註');
            $table->date('scrap_date')->nullable()->comment('報廢日期');
            $table->boolean('is_scraped')->nullable()->comment('是否已報廢');
            $table->dropColumn('key_name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('keys', function (Blueprint $table) {
            $table->dropColumn('comment');
            $table->dropColumn('scrap_date');
            $table->dropColumn('is_scraped');
            $table->string('key_name')->comment('鑰匙代號');
        });
    }
}
