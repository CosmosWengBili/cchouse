<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsToDeposits extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('deposits', function (Blueprint $table) {
            $table->string('payer_name')->default('')->comment('付訂人姓名');
            $table->string('payer_certification_number')->default('')->comment('付訂人證號');
            $table->string('payer_is_legal_person')->default(false)->comment('付訂人是否為法人');
            $table->string('payer_phone')->default('')->comment('電話');
            $table->unsignedBigInteger('receiver')->nullable()->comment('收訂金的專員');
            $table->date('appointment_date')->nullable()->comment('約定起租日');

            $table->foreign('receiver')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('deposits', function (Blueprint $table) {
            $table->dropForeign('deposits_receiver_foreign');

            $table->dropColumn('payer_name');
            $table->dropColumn('payer_certification_number');
            $table->dropColumn('payer_is_legal_person');
            $table->dropColumn('payer_phone');
            $table->dropColumn('receiver');
            $table->dropColumn('appointment_date');
        });
    }
}
