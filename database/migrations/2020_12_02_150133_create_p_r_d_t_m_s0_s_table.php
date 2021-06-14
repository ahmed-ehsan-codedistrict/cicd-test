<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePRDTMS0STable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('PRDTMS0')) {
            Schema::create('PRDTMS0', function (Blueprint $table) {
                $table->integer('CONO3L');
                $table->string('PRCD3L');
                $table->string('CRCD3L');
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
        Schema::dropIfExists('PRDTMS0');
    }
}
