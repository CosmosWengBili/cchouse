<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeLandlordsColumnsNullable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('landlords', function (Blueprint $table) {
            $table->string('invoice_collection_method')->nullable()->comment('發票領取方式')->change();
            $table->string('invoice_collection_number')->nullable()->comment('發票領取號碼')->change();
            $table->string('invoice_mailing_address')->nullable()->comment('發票寄送地址')->change();
            $table->boolean('is_legal_person')->nullable()->comment('是否為法人')->change();
            $table->string('residence_address')->nullable()->comment('戶籍地址')->change();
            $table->boolean('is_collected_by_third_party')->nullable()->comment('是否第三人代收')->change();
            $table->string('birth')->nullable()->comment('生日')->change();
            $table->integer('bank_code')->nullable()->comment('匯款銀行')->change();
            $table->string('branch_code')->nullable()->comment('匯款分行')->change();
            $table->string('account_name')->nullable()->comment('戶名')->change();
            $table->string('account_number')->nullable()->comment('帳號')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('landlords', function (Blueprint $table) {
            $table->string('invoice_collection_method')->comment('發票領取方式')->change();
            $table->string('invoice_collection_number')->comment('發票領取號碼')->change();
            $table->string('invoice_mailing_address')->comment('發票寄送地址')->change();
            $table->boolean('is_legal_person')->comment('是否為法人')->change();
            $table->string('residence_address')->comment('戶籍地址')->change();
            $table->boolean('is_collected_by_third_party')->comment('是否第三人代收')->change();
            $table->string('birth')->comment('生日')->change();
            $table->integer('bank_code')->comment('匯款銀行')->change();
            $table->string('branch_code')->comment('匯款分行')->change();
            $table->string('account_name')->comment('戶名')->change();
            $table->string('account_number')->comment('帳號')->change();
        });
    }
}
