<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ReorganizeOrderOfColumnDueTime extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE tenant_electricity_payments MODIFY COLUMN due_time DATE AFTER ammeter_read_date");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE tenant_electricity_payments MODIFY COLUMN due_time DATE AFTER deleted_at");
    }
}
