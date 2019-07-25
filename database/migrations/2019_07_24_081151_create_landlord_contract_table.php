<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLandlordContractTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('landlord_contract', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('building_id')->comment('物件 ID ');
            $table->unsignedBigInteger('landlord_id')->comment('房東 ID');
            $table->unsignedBigInteger('commissioner_id')->comment('專員 ID');
            $table->string('commission_type')->comment('承租方式');
            $table->date('commission_start_date')->comment('委託起');
            $table->date('commission_end_date')->comment('委託迄');
            $table->date('warranty_start_date')->comment('保固起');
            $table->date('warranty_end_date')->comment('保固迄');
            $table->date('rental_decoration_free_start_date')->comment('免租金裝潢起');
            $table->date('rental_decoration_free_end_date')->comment('免租金裝潢迄');
            $table->integer('annual_service_fee_month_count')->comment('年繳服務費月數');
            $table->integer('charter_fee')->comment('包租費用');
            $table->integer('taxable_charter_fee')->comment('包租報稅費用');
            $table->string('rent_collection_frequency')->comment('收租頻率');
            $table->integer('rent_collection_time')->comment('收租時間');
            $table->date('rent_adjusted_date')->comment('租金調整日');
            $table->float('adjust_ratio')->comment('調整%數');
            $table->integer('deposit_month_count')->comment('押金月數');
            $table->boolean('is_collected_by_third_party')->comment('是否代收');
            $table->boolean('is_notarized')->comment('是否公證');
            $table->integer('bank_code')->comment('匯款銀行');
            $table->integer('branch_code')->comment('匯款分行');
            $table->string('account_name')->comment('戶名');
            $table->string('account_number')->comment('帳號');
            $table->string('invoice_collection_method')->comment('發票領取方式');
            $table->string('invoice_collection_number')->comment('發票領取號碼');
            $table->string('invoice_mailing_address')->comment('發票寄送地址');
            
            $table->timestamps();
            $table->softDeletes();
            

            $table->foreign('building_id')
                ->references('id')->on('buildings');

            $table->foreign('landlord_id')
                ->references('id')->on('landlords');

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
        Schema::dropIfExists('landlord_contract');
    }
}
