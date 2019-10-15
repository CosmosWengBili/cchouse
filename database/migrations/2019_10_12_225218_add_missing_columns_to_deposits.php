<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMissingColumnsToDeposits extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('deposits', function (Blueprint $table) {
            $table->string('returned_method')->nullable()->comment('退訂方式');
            $table->string('returned_serial_number')->nullable()->comment('退訂單號');
            $table->string('returned_bank')->nullable()->comment('退訂銀行');
            $table->integer('company_allocation_amount')->nullable()->comment('公司分配金額');
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
            $table->dropColumn('returned_method');
            $table->dropColumn('returned_serial_number');
            $table->dropColumn('returned_bank');
            $table->dropColumn('company_allocation_amount');
        });
    }
}
