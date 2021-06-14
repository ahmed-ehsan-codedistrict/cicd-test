<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGDTSNewOrderTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('GDTS_NewOrderType')) {
            Schema::create('GDTS_NewOrderType', function (Blueprint $table) {
                $table->integer('Companyno');
                $table->string('OrderType');
                $table->primary(['Companyno','OrderType']);
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
        Schema::dropIfExists('GDTS_NewOrderType');
    }
}