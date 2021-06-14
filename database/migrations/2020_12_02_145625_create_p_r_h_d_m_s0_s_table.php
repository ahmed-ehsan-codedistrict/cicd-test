<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePRHDMS0STable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('PRHDMS0')) {
            Schema::create('PRHDMS0', function (Blueprint $table) {
                $table->integer('CONO3K');
                $table->string('PRCD3K')->unique();
                $table->string('PRDS3K');
                $table->string('SHDS3K');
                $table->string('EXDS3K');
                $table->string('CLCD3K');
                $table->string('SCCD3K');
                $table->integer('DVNO3K');
                $table->string('MKGP3K');
                $table->string('STCD3K');
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
        Schema::dropIfExists('PRHDMS0');
    }
}
