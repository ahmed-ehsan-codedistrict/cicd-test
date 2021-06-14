<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMKGPMS0STable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('MKGPMS0')) {
            Schema::create('MKGPMS0', function (Blueprint $table) {
                $table->integer('CONO3N');
                $table->string('MKGP3N');
                $table->string('MKDS3N');
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
        Schema::dropIfExists('MKGPMS0');
    }
}
