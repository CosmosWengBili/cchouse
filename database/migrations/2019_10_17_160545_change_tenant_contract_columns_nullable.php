<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeTenantContractColumnsNullable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tenant_contract', function (Blueprint $table) {
            $table->string('other_rights')->nullable()->comment('他項權利種類')->change();
            $table->integer('220v_start_degree')->nullable()->comment('220v 起度')->change();
            $table->integer('220v_end_degree')->nullable()->comment('220v 結度')->change();
            $table->string('invoice_collection_number')->nullable()->comment('載具號碼')->change();
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
            $table->string('other_rights')->comment('他項權利種類')->change();
            $table->integer('220v_start_degree')->comment('220v 起度')->change();
            $table->integer('220v_end_degree')->comment('220v 結度')->change();
            $table->string('invoice_collection_number')->comment('載具號碼')->change();
        });
    }
}
