<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterPayLogsCommentToNullable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pay_logs', function (Blueprint $table) {
            $table->string('comment')->nullable()->comment('備註')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pay_logs', function (Blueprint $table) {
            $table->string('comment')->comment('備註')->change();
        });
    }
}
