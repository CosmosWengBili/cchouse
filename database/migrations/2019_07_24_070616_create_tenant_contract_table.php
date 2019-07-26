<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTenantContractTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tenant_contract', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('room_id')->comment('室ID');
            $table->unsignedBigInteger('tenant_id')->comment('租客ID');
            $table->string('contract_serial_number')->comment('契約序號');
            $table->boolean('set_other_rights')->comment('設定他項權利');
            $table->string('other_rights')->comment('他項權利種類');
            $table->boolean('sealed_registered')->comment('查封登記');
            $table->string('car_parking_floor')->comment('汽車停車層');
            $table->string('car_parking_type')->comment('汽車停車種類');
            $table->string('car_parking_space_number')->comment('汽車停車編號');
            $table->string('motorcycle_parking_floor')->comment('機車停車層');
            $table->string('motorcycle_parking_space_number')->comment('機車停車編號');
            $table->integer('motorcycle_parking_count')->comment('機車停車個數');
            $table->boolean('effective')->comment('是否已生效');
            $table->date('contract_start')->nullable()->comment('租約起');
            $table->date('contract_end')->nullable()->comment('租約迄');
            $table->integer('rent')->comment('租金');
            $table->integer('rent_pay_day')->comment('租金支付日');
            $table->integer('deposit')->comment('押金');
            $table->integer('deposit_paid')->comment('押金已繳納');
            $table->string('electricity_payment_method')->comment('電費繳款方式');
            $table->string('electricity_calculate_method')->comment('電費計算方式');
            $table->float('electricity_price_per_degree')->comment('電費費率');
            $table->float('electricity_price_per_degree_summer')->comment('電費夏季費率');
            $table->integer('110v_start_degree')->comment('110v 起度');
            $table->integer('220v_start_degree')->comment('220v 起度');
            $table->integer('110v_end_degree')->comment('110v 結度');
            $table->integer('220v_end_degree')->comment('220v 結度');
            $table->float('commissioner_rate')->comment('專員服務費費率');
            $table->string('invoice_collection_method')->comment('發票領取方式');
            $table->string('invoice_collection_number')->comment('發票領取號碼');

            $table->timestamps();
            $table->softDeletes();

            
            $table->foreign('room_id')
                ->references('id')->on('rooms');
            $table->foreign('tenant_id')
                ->references('id')->on('tenants');
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tenant_contract');
    }
}
