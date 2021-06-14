<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProdPLMSTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('ProdPLM')) {
            Schema::create('ProdPLM', function (Blueprint $table) {
                $table->integer('CompanyNo');
                $table->string('Style');
                $table->string('Market');
                $table->string('Season');
                $table->string('Brand');
                $table->string('Designer');
                $table->string('FabType');
                $table->string('FabricName');
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
        Schema::dropIfExists('ProdPLM');
    }
}
