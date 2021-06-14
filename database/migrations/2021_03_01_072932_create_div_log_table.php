<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDivLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('DivLogo')) {
            Schema::create('DivLogo', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->integer('CompanyNo');
                $table->integer('DivisionNo');
                $table->string('LogoName', 50);
                $table->string('LogoFileName', 50);
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
        Schema::dropIfExists('DivLogo');
    }
}
