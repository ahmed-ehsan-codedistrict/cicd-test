<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCSSRMS0STable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('CSSRMS0')) {
            Schema::create('CSSRMS0', function (Blueprint $table) {
                $table->integer('CONOW8');
                $table->integer('CSNOW8');
                $table->string('SRCDW8');
                $table->string('SRDSW8');
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
        Schema::dropIfExists('CSSRMS0');
    }
}