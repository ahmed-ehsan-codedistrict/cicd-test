<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSLMNMS0STable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('SLMNMS0')) {
            Schema::create('SLMNMS0', function (Blueprint $table) {
                $table->integer('CONO2H');
                $table->integer('SMNO2H');
                $table->string('FLNM2H');
                $table->string('SHNM2H');
                $table->string('SMCD2H');
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
        Schema::dropIfExists('SLMNMS0');
    }
}
