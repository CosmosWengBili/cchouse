<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIsDoubtfulToPayOffsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pay_offs', function (Blueprint $table) {
            //
            $table->boolean('is_doubtful')->default(0)->after('tenant_contract_id')->comment('是否為呆帳');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pay_offs', function (Blueprint $table) {
            $table->dropColumn('is_doubtful');
        });
    }
}
