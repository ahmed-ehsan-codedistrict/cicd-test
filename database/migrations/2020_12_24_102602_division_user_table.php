<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DivisionUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('DivisionUser')) {
            Schema::create('DivisionUser', function (Blueprint $table) {
                $table->integer('CompanyNo');
                $table->integer('UserId');
                $table->integer('DivisionNo');
                $table->primary(['CompanyNo','UserId','DivisionNo']);
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
        Schema::dropIfExists('DivisionUser');
    }
}
