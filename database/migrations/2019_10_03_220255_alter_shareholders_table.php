<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterShareholdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shareholders', function (Blueprint $table) {
            $table->renameColumn('email', 'contact_method');
            $table->integer('exchange_fee')->default(0)->comment('匯費');
            $table->string('bank_branch')->comment('銀行分行');
//            $table->integer('distribution_amount')->comment('分配金額');
            $table->string('method')->comment('方式');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('shareholders', function (Blueprint $table) {
            $table->renameColumn('contact_method', 'email');
            $table->dropColumn('exchange_fee');
            $table->dropColumn('bank_branch');
            $table->dropColumn('distribution_amount');
            $table->dropColumn('method');
        });
    }
}
