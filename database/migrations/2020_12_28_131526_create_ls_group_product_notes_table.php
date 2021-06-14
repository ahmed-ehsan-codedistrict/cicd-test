<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLsGroupProductNotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Line Sheet Group Products Private Notes
        if (!Schema::hasTable('LSGPPrivatNotes')) {
            Schema::create('LSGPPrivatNotes', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->integer('CompanyNo');
                $table->integer('LSGPId');
                $table->integer('UserId');
                $table->string('ProductId')->nullable();
                $table->string('Notes');
                $table->dateTime('UpdatedDate')->nullable();
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
        Schema::dropIfExists('LSGPPrivatNotes');
    }
}
