<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDIVNMS0STable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('DIVNMS0')) {
            Schema::create('DIVNMS0', function (Blueprint $table) {
                $table->integer('CONO3C');
                $table->integer('DVNO3C');
                $table->string('DVNM3C');
                $table->string('UPNM3C');
                $table->string('SBAP3C');
                $table->integer('UPSL3C');
                $table->string('RNNO3C');
                $table->string('INLS3C');
                $table->string('INOR3C');
                $table->integer('RPSQ3C');
                $table->string('RQCS3C');
                $table->string('RQWH3C');
                //$table->timestamps();
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
        Schema::dropIfExists('DIVNMS0');
    }
}