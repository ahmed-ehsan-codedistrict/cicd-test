<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('Options'))
        {
            Schema::create('Options', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('DisplayID');
                $table->string('DisplayValue');
                $table->string('TableName');
                $table->string('TableColumn');
                $table->integer('CompanyNo');
                $table->timestamps();
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
        Schema::dropIfExists('Options');
    }
}
