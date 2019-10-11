<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateIsDepositsCollectedComment extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('deposits', function (Blueprint $table) {
            $table->boolean('is_deposit_collected')->comment('已簽約')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('deposits', function (Blueprint $table) {
            $table->boolean('is_deposit_collected')->comment('已收訂')->change();
        });
    }
}
