<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDistributionAmountToShareholders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shareholders', function (Blueprint $table) {
            $table->integer('distribution_amount')->after('distribution_rate')->comment('固定分配費用');
            $table->decimal('distribution_rate', 5,2)->change();
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
            $table->dropColumn('distribution_amount');
            $table->string('distribution_rate');
        });
    }
}
