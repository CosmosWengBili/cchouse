<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddInvoiceAttributesToLandlordOtherSubjects extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('landlord_other_subjects', function (Blueprint $table) {
            $table->boolean('is_invoiced')->nullable()->comment('是否開發票');
            $table->string('invoice_item_name')->nullable()->comment('發票科目名稱');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('landlord_other_subjects', function (Blueprint $table) {
            $table->dropColumn('is_invoiced');
            $table->dropColumn('invoice_item_name');
        });
    }
}
