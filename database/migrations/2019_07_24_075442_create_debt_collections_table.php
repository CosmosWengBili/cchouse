<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDebtCollectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('debt_collections', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('colloector_id')->comment('催收人');
            $table->unsignedBigInteger('tenant_contract_id')->comment('租客合約ID');
            $table->text('details')->comment('催收說明');
            $table->string('status')->comment('催收狀態');
            $table->boolean('is_penalty_collected')->comment('是否收滯納金');
            $table->text('comment')->comment('備註');

            // 催收產生日, 最後更新日
            $table->timestamps();
            $table->softDeletes();


            $table->foreign('colloector_id')
                ->references('id')->on('users');

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
        Schema::dropIfExists('debt_collections');
    }
}
