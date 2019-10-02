<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsToKeyRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('key_requests', function (Blueprint $table) {
            $table->date('borrow_date')->nullable()->comment('預計借日');
            $table->date('return_date')->nullable()->comment('預計還日');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('key_requests', function (Blueprint $table) {
            $table->dropColumn('borrow_date');
            $table->dropColumn('return_date');
        });
    }
}
