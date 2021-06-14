<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProdFitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('ProdFit')) {
            Schema::create('ProdFit', function (Blueprint $table) {
                $table->integer('CompanyNo');
                $table->string('Style');
                $table->integer('id_attribute');
                $table->string('attribname');
                $table->string('attribval');
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
        Schema::dropIfExists('ProdFit');
    }
}
