<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateORTPMS0STable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('ORTPMS0')) {
            Schema::create('ORTPMS0', function (Blueprint $table) {
                $table->integer('CONO3Q');
                $table->string('TPCD3Q');
                $table->string('TPDS3Q');
                $table->string('RCRQ3Q');
                $table->integer('ORPR3Q');
                $table->string('RQBK3Q');
                $table->string('ALPK3Q');
                $table->string('ALRE3Q');
                $table->string('APCL3Q');
                $table->string('FRPT3Q');
                $table->integer('INTP3Q');
                $table->integer('WHNO3Q');
                $table->string('LCCD3Q');
                $table->string('STCD3Q');
                $table->string('CSEX3Q');
                $table->string('FGTP3Q');
                $table->integer('SLAC3Q');
                $table->integer('SLDP3Q');
                $table->integer('SLSA3Q');
                $table->integer('TDAC3Q');
                $table->integer('TDDP3Q');
                $table->integer('TDSA3Q');
                $table->integer('SHAC3Q');
                $table->integer('SHDP3Q');
                $table->integer('SHSA3Q');
                $table->integer('TXAC3Q');
                $table->integer('TXDP3Q');
                $table->integer('TXSA3Q');
                $table->integer('COAC3Q');
                $table->integer('CODP3Q');
                $table->integer('COSA3Q');
                $table->integer('DAAC3Q');
                $table->integer('DADP3Q');
                $table->integer('DASA3Q');
                $table->string('Ecommerce',1)->nullable();
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
        Schema::dropIfExists('ORTPMS0');
    }
}