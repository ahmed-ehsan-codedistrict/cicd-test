<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePRCLMS0STable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('PRCLMS0')) {
            Schema::create('PRCLMS0', function (Blueprint $table) {
                $table->integer('CONO3D');
                $table->string('CLCD3D');
                $table->string('CLDS3D');
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
        Schema::dropIfExists('PRCLMS0');
    }
}
