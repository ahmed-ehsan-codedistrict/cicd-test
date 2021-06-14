<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSZRTMS0STable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('SZRTMS0')) {
            Schema::create('SZRTMS0', function (Blueprint $table) {
                $table->integer('CONOK1');
                $table->string('SZCDK1');
                $table->string('SRCDK1');
                $table->string('SRDSK1');
                $table->integer('SR01K1');
                $table->integer('SR02K1');
                $table->integer('SR03K1');
                $table->integer('SR04K1');
                $table->integer('SR05K1');
                $table->integer('SR06K1');
                $table->integer('SR07K1');
                $table->integer('SR08K1');
                $table->integer('SR09K1');
                $table->integer('SR10K1');
                $table->integer('SR11K1');
                $table->integer('SR12K1');
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
        Schema::dropIfExists('SZRTMS0');
    }
}