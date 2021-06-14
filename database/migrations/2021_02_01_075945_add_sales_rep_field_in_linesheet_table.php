<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSalesRepFieldInLinesheetTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('LineSheets')) {
            Schema::table('LineSheets', function (Blueprint $table) {
                $table->integer('customerId') -> nullable(true) -> change();
                $table->integer('Division')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('linesheet', function (Blueprint $table) {
            //
        });
    }
}
