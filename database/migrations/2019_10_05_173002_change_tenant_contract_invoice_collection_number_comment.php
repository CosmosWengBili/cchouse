<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeTenantContractInvoiceCollectionNumberComment extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tenant_contract', function (Blueprint $table) {
            $table->string('invoice_collection_number')->comment('載具號碼')->change();
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
            $table->string('invoice_collection_number')->comment('發票領取號碼')->change();
        });
    }
}
