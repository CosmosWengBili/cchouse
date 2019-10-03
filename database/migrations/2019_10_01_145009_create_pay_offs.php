<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePayOffs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pay_offs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('pay_off_type')->comment('退租方式');
            $table->integer('110v_degree')->comment('110v度數');
            $table->integer('220v_degree')->comment('220v度數');
            $table->json('payment_detail')->comment('帳單細節');
            $table->integer('tenant_amount')->comment('租客應退');
            $table->integer('company_income')->comment('兆基應收');
            $table->integer('landlord_paid')->comment('房東應付');
            $table->bigInteger('tenant_contract_id')->unsigned();
            $table->foreign('tenant_contract_id')->references('id')->on('tenant_contract');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pay_offs');
    }
}
