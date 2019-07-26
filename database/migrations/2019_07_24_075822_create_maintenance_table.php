<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMaintenanceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('maintenance', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('tenant_contract_id')->comment('租客合約 ID');
            $table->date('reported_at')->nullable()->comment('反映日期');
            $table->date('expected_service_date')->nullable()->comment('預計處理日期');
            $table->time('expected_service_time')->nullable()->comment('預計處理時間');
            $table->date('dispatch_date')->nullable()->comment('派工日');
            $table->unsignedBigInteger('commissioner_id')->nullable()->comment('處理專員');
            $table->unsignedBigInteger('maintenance_staff_id')->nullable()->comment('維修人員');
            $table->date('closed_date')->nullable()->comment('結案日期');
            $table->text('closed_comment')->comment('完工備註');
            $table->text('service_comment')->comment('處理備註');
            $table->string('status')->comment('狀態');
            $table->text('incident_details')->comment('事故說明');
            $table->string('incident_type')->comment('事故類別');
            $table->string('work_type')->comment('工種');
            $table->integer('number_of_times')->comment('趟數');
            $table->date('payment_request_date')->nullable()->comment('請款日期');
            $table->string('closing_serial_number')->comment('結案單號');
            $table->text('billing_details')->comment('登帳說明');
            $table->string('payment_request_serial_number')->comment('請款單號');
            $table->integer('cost')->comment('成本');
            $table->integer('price')->comment('複價');
            $table->boolean('is_recorded')->comment('是否入帳');
            $table->string('invoice_serail_number')->comment('發票號碼');
            $table->text('comment')->comment('備註');

            $table->timestamps();
            $table->softDeletes();


            $table->foreign('tenant_contract_id')
                ->references('id')->on('tenant_contract');

            $table->foreign('commissioner_id')
                ->references('id')->on('users');

            $table->foreign('maintenance_staff_id')
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
        Schema::dropIfExists('maintenance');
    }
}
