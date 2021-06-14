<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLineSheetShareTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('LineSheetShare')) {
            Schema::create('LineSheetShare', function (Blueprint $table) {
                $table->bigIncrements('Id');
                $table->integer("CompanyNo");
                $table->integer("ShareBy");    // can be used as Created by
                $table->integer("ShareTo");
                $table->integer("LineSheetId");
                $table->integer("UpdatedBy")->nullable();
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
        Schema::dropIfExists('LineSheetShare');
    }
}
