<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterEditorialReviewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('editorial_reviews', function (Blueprint $table) {
            $table->json('original_value')->change();
            $table->json('edit_value')->change();
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
            $table->string('original_value')->change();
            $table->string('edit_value')->change();
        });
    }
}
