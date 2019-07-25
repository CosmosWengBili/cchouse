<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCompanyIncomesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('company_incomes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('tenant_contract_id')->comment('租客合約ID');
            $table->string('subject')->comment('項目');
            $table->date('income_date')->nullable()->comment('收入時間');
            $table->integer('amount')->comment('費用');
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
        Schema::dropIfExists('company_incomes');
    }
}
