<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomSortCombinationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('CustomSortCombination', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('CompanyNo');

            $table->string('SortByColumn');

            $table->integer('UserId');
            $table->integer('GroupId');
            $table->integer('LineSheetId');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('custom_sort_combination');
    }
}
