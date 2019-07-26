<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTenantElectricityPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tenant_electricity_payments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('tenant_contract_id')->comment('租客合約ID');
            $table->date('ammeter_read_date')->nullable()->comment('抄表時間');
            $table->integer('110v_start_degree')->comment('110v起');
            $table->integer('110v_end_degree')->comment('110v迄');
            $table->integer('220v_start_degree')->comment('220v起');
            $table->integer('220v_end_degree')->comment('220v迄');
            $table->integer('amount')->comment('費用');
            $table->string('invoice_serial_number')->comment('發票號碼');
            $table->boolean('is_charge_off_done')->comment('是否已沖銷');
            $table->text('comment')->comment('備註');

            $table->timestamps();
            $table->softDeletes();


            $table->foreign('tenant_contract_id')
                ->references('id')->on('tenant_contract');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tenant_electricity_payments');
    }
}
