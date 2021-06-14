<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLinesheetGroupProductTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('LineSheetGroupProducts')) {
            Schema::create('LineSheetGroupProducts', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->integer('CompanyNo');
                $table->integer('GroupId');
                $table->string('ProductId');
                $table->string('ColorId')->nullable();
                $table->string('PublicNotes')->nullable();
                $table->string('PublicNotesCreatedBy')->nullable();
                $table->integer('CreatedBy');
                $table->integer('UpdatedBy')->nullable();
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
        Schema::dropIfExists('LineSheetGroupProducts');
    }
}
