<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterColumnsInTenantContractTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tenant_contract', function (Blueprint $table) {
            //
            $table->integer('110v_end_degree')->nullable()->change();
            $table->integer('220v_start_degree')->nullable()->change();
            $table->integer('220v_end_degree')->nullable()->change();
            $table->string('invoice_collection_method')->nullable()->change();
            $table->string('invoice_collection_number')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tenant_contract', function (Blueprint $table) {
            $table->integer('110v_end_degree')->nullable(false)->change();
            $table->integer('220v_start_degree')->nullable(false)->change();
            $table->integer('220v_end_degree')->nullable(false)->change();
            $table->string('invoice_collection_method')->nullable(false)->change();
            $table->string('invoice_collection_number')->nullable(false)->change();
        });
    }
}
