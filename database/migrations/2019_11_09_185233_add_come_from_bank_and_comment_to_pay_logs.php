<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddComeFromBankAndCommentToPayLogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pay_logs', function (Blueprint $table) {
            $table->string('come_from_bank')->nullable()->comment('來源銀行');
            $table->integer('pay_sum')->nullable()->comment('匯款總額');
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
            $table->dropColumn('come_from_bank');
            $table->dropColumn('pay_sum');
        });
    }
}
