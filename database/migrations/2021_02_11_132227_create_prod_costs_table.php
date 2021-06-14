<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProdCostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('ProdCost')) {
            Schema::create('ProdCost', function (Blueprint $table) {
                $table->integer('CompanyNo');
                $table->string('Style');
                $table->string('Color');
                $table->integer('Revision');
                $table->string('Version');
                $table->string('Factory');
                $table->string('FactoryName')->nullable();
                $table->decimal('Cost',18,4);
                $table->string('Origin',1);
                $table->dateTime('ModifiedOn')->nullable();
                $table->string('Country')->nullable();
                $table->string('ProdType');
                $table->tinyInteger('CostSheet');
                $table->tinyInteger('FaceCard');
                $table->decimal('ProdCut',9,0)->nullable();
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
        Schema::dropIfExists('ProdCost');
    }
}
