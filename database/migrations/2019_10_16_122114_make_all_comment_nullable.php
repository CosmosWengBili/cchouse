<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MakeAllCommentNullable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('appliances', function (Blueprint $table) {
            $table->text('comment')->nullable()->comment('備註')->change();
        });
        Schema::table('buildings', function (Blueprint $table) {
            $table->text('comment')->nullable()->comment('備註')->change();
        });
        Schema::table('company_incomes', function (Blueprint $table) {
            $table->text('comment')->nullable()->comment('備註')->change();
        });
        Schema::table('debt_collections', function (Blueprint $table) {
            $table->text('comment')->nullable()->comment('備註')->change();
        });
        Schema::table('landlord_other_subjects', function (Blueprint $table) {
            $table->text('comment')->nullable()->comment('備註')->change();
        });
        Schema::table('landlord_payments', function (Blueprint $table) {
            $table->text('comment')->nullable()->comment('備註')->change();
        });
        Schema::table('landlords', function (Blueprint $table) {
            $table->text('note')->nullable()->comment('備註')->change();
        });
        Schema::table('maintenances', function (Blueprint $table) {
            $table->text('comment')->nullable()->comment('備註')->change();
        });
        Schema::table('reversal_error_cases', function (Blueprint $table) {
            $table->text('comment')->nullable()->comment('備註')->change();
        });
        Schema::table('rooms', function (Blueprint $table) {
            $table->text('comment')->nullable()->comment('備註')->change();
        });
        Schema::table('tenant_contract', function (Blueprint $table) {
            $table->text('comment')->nullable()->comment('備註')->change();
        });
        Schema::table('tenant_electricity_payments', function (Blueprint $table) {
            $table->text('comment')->nullable()->comment('備註')->change();
        });
        Schema::table('tenant_payments', function (Blueprint $table) {
            $table->text('comment')->nullable()->comment('備註')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('appliances', function (Blueprint $table) {
            $table->text('comment')->comment('備註')->change();
        });
        Schema::table('buildings', function (Blueprint $table) {
            $table->text('comment')->change();
        });
        Schema::table('company_incomes', function (Blueprint $table) {
            $table->text('comment')->comment('備註')->change();
        });
        Schema::table('debt_collections', function (Blueprint $table) {
            $table->text('comment')->comment('備註')->change();
        });
        Schema::table('landlord_other_subjects', function (Blueprint $table) {
            $table->text('comment')->comment('備註')->change();
        });
        Schema::table('landlord_payments', function (Blueprint $table) {
            $table->text('comment')->comment('備註')->change();
        });
        Schema::table('landlords', function (Blueprint $table) {
            $table->text('note')->comment('備註')->change();
        });
        Schema::table('maintenances', function (Blueprint $table) {
            $table->text('comment')->comment('備註')->change();
        }); 
        Schema::table('reversal_error_cases', function (Blueprint $table) {
            $table->string('comment')->comment('備註')->change();
        });
        Schema::table('rooms', function (Blueprint $table) {
            $table->text('comment')->comment('備註')->change();
        });
        Schema::table('tenant_contract', function (Blueprint $table) {
            $table->text('comment')->comment('備註')->change();
        });
        Schema::table('tenant_electricity_payments', function (Blueprint $table) {
            $table->text('comment')->comment('備註')->change();
        });
        Schema::table('tenant_payments', function (Blueprint $table) {
            $table->text('comment')->comment('備註')->change();
        });
    }
}
