<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsToPayLogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pay_logs', function (Blueprint $table) {
            $table->timestamp('deposit_at')->nullable()->after('paid_at')->comment('存入時間');
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
            $table->dropColumn('deposit_at');
        });
    }
}
