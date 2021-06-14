<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCUSTMS0STable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('CUSTMS0')) {
            Schema::create('CUSTMS0', function (Blueprint $table) {
                $table->integer('CONO2S');
                $table->integer('CSNO2S');
                $table->string('FLNM2S');
                $table->string('SHNM2S');
                $table->string('EXNM2S');
                $table->string('DBNM2S');
                $table->string('CLCD2S');
                $table->string('SCCD2S');
                $table->string('RGCD2S');
                $table->string('TRCD2S');
                $table->string('CPCD2S');
                $table->string('STCD2S');
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
        Schema::dropIfExists('CUSTMS0');
    }
}
