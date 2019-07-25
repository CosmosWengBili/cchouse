<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTenantPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tenant_payments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('tenant_contract_id')->comment('租客合約ID');
            $table->string('subject')->comment('項目');
            $table->date('due_time')->nullable()->comment('應繳時間');
            $table->integer('amount')->comment('費用');
            $table->boolean('is_charge_off_done')->comment('是否已沖銷');
            $table->date('charge_off_date')->nullable()->comment('沖銷日期');
            $table->string('invoice_serial_numner')->comment('發票號碼');
            $table->string('collected_by')->comment('收取者');
            $table->boolean('is_visible_at_report')->comment('是否顯示在報表');
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
        Schema::dropIfExists('tenant_payments');
    }
}
