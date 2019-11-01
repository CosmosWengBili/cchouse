<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsToAppliancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('appliances', function (Blueprint $table) {
            $table->date('purchased_date')->comment('購買日期')->after('comment');
            $table->date('warranty_date')->comment('保固日期')->after('purchased_date');

            $table->string('maintenance_phone')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('appliances', function (Blueprint $table) {
            $table->dropColumn('purchased_date');
            $table->dropColumn('warranty_date');

            $table->string('maintenance_phone')->nullable(false)->change();
        });
    }
}
