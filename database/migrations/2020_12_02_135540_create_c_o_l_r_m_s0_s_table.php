<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCOLRMS0STable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('COLRMS0')) {
            Schema::create('COLRMS0', function (Blueprint $table) {
                $table->integer('CONO3J');
                $table->string('CRCD3J');
                $table->string('CRDS3J');
                $table->string('CDES3J');
                $table->integer('NCLR3J');
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
        Schema::dropIfExists('COLRMS0');
    }
}
