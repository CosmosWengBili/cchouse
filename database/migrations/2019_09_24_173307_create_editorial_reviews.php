<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEditorialReviews extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('editorial_reviews', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer("editable_id");
            $table->string("editable_type");
            $table->string('status')->comment('狀態')->default('待審核');
            $table->string('original_value')->comment('原始資料');
            $table->string('edit_value')->comment('修改資料');
            $table->integer('edit_user')->comment('修改人');
            $table->string('comment')->comment('備註')->nullable();

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
        Schema::dropIfExists('editorial_reviews');
    }
}
