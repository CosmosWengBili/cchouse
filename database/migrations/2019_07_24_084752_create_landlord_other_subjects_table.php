<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLandlordOtherSubjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('landlord_other_subjects', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('building_id')->comment('物件ID');
            $table->string('subject')->comment('科目');
            $table->string('subject_type')->comment('科目類別');
            $table->string('income_or_expense')->comment('收入支出');
            $table->date('expense_date')->comment('費用日期');
            $table->integer('amount')->comment('費用');
            $table->text('comment')->comment('備註');

            $table->timestamps();
            $table->softDeletes();


            $table->foreign('building_id')
                ->references('id')->on('buildings');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('landlord_other_subjects');
    }
}
