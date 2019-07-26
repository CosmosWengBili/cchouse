<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCommissionerIdForeignKeyToTenantContractTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tenant_contract', function (Blueprint $table) {
            $table->unsignedBigInteger('commissioner_id')->nullable()->comment('專員');

            $table->foreign('commissioner_id')
                ->references('id')->on('users');
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
            if(Schema::hasColumn('tenant_contract', 'commissioner_id')) {
                $table->dropColumn('commissioner_id');
            }
        });
    }
}
