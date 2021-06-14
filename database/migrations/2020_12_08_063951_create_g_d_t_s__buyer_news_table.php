<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGDTSBuyerNewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('GDTS_BuyerNew')) {
            Schema::create('GDTS_BuyerNew', function (Blueprint $table) {
                $table->integer('CompanyNo');
                $table->integer('Custno');
                $table->integer('Divno');
                $table->string('BuyerName');
                $table->integer('Buyno')->nullable();
                $table->string('Addr1')->nullable();
                $table->string('Addr2')->nullable();
                $table->string('City')->nullable();
                $table->string('State')->nullable();
                $table->string('Zip')->nullable();
                $table->string('Country')->nullable();
                $table->integer('Phone')->nullable();
                $table->integer('Fax')->nullable();
                $table->string('Email')->nullable();
                $table->string('Position')->nullable();
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
        Schema::dropIfExists('GDTS_BuyerNew');
    }
}