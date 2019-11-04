<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIsInvoicedToLandlordPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('landlord_payments', function (Blueprint $table) {
            $table->boolean('is_invoiced')->default(1)->comment('已開票');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('landlord_payments', function (Blueprint $table) {
            $table->dropColumn('is_invoiced');
        });
    }
}
