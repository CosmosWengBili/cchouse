<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterColumnsInBuildingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('buildings', function (Blueprint $table) {
            $table->string('land_use')->nullable()->after('legal_usage')->comment('土地使用分區');
            $table->string('commission_group')->nullable()->after('commissioner_id')->comment('招租組別');

            $table->string('accounting_group')->comment('管理組別')->change();
            $table->string('group')->comment('帳務組別')->change();

            //garbage_collection_time
            $table->string('shared_electricity')->nullable()->change();
            $table->string('private_ammeter_location')->nullable()->change();
            $table->string('garbage_collection_location')->nullable()->change();
            $table->string('garbage_collection_time')->nullable()->change();
            $table->string('distribution_method')->nullable()->change();
            $table->string('accounting_group')->nullable()->change();
            $table->string('rental_receipt')->nullable()->change();
            $table->string('group')->nullable()->change();
            $table->string('electricity_payment_method')->nullable()->change();

            //
            $table->boolean('is_squatter')->default(0)->after('address')->comment('是否違建');
            $table->string('squatter_status')->nullable()->after('is_squatter')->comment('違建狀態');
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
            $table->dropColumn('land_use');
            $table->dropColumn('commission_group');

            $table->string('accounting_group')->comment('會計組別')->change();
            $table->string('group')->comment('組別')->change();

            //
            $table->string('shared_electricity')->nullable(false)->change();
            $table->string('private_ammeter_location')->nullable(false)->change();
            $table->string('garbage_collection_location')->nullable(false)->change();
            $table->string('garbage_collection_time')->nullable(false)->change();
            $table->string('distribution_method')->nullable(false)->change();
            $table->string('accounting_group')->nullable(false)->change();
            $table->string('rental_receipt')->nullable(false)->change();
            $table->string('group')->nullable(false)->change();
            $table->string('electricity_payment_method')->nullable(false)->change();

            $table->dropColumn('is_squatter');
            $table->dropColumn('squatter_status');
        });
    }
}
