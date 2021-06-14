<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGDTSBuyTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('GDTS_BuyType')) {
            Schema::create('GDTS_BuyType', function (Blueprint $table) {
                $table->integer('Companyno');
                $table->integer('Custno');
                $table->string('BuyType');
                $table->string('BuyTypeDesc');
                $table->primary(['CompanyNo','Custno','BuyType']);
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
        Schema::dropIfExists('GDTS_BuyType');
    }
}