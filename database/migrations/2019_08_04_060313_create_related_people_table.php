<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRelatedPeopleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('related_people', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->morphs('related_person');
            $table->string('type')->comment('關係人種類');
            $table->string('name')->comment('姓名');
            $table->string('phone')->comment('電話');
            $table->string('relationship')->comment('關係');


            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('related_people');
    }
}
