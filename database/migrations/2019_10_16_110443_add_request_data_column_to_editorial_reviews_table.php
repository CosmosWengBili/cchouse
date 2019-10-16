<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRequestDataColumnToEditorialReviewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('editorial_reviews', function (Blueprint $table) {
            $table->json('extra_data')->nullable()->comment('審核通過後需要使用到的資料');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('editorial_reviews', function (Blueprint $table) {
            $table->dropColumn('extra_data');
        });
    }
}
