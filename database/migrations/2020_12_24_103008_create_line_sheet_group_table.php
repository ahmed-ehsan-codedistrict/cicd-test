<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLineSheetGroupTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('LineSheetGroup')) {
            Schema::create('LineSheetGroup', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->integer('CompanyNo');
                $table->string('GroupName');
                $table->integer('LineSheetId');
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
        Schema::dropIfExists('LineSheetGroup');
    }
}
