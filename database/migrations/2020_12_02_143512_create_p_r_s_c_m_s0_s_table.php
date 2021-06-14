<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePRSCMS0STable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('PRSCMS0')) {
            Schema::create('PRSCMS0', function (Blueprint $table) {
                $table->integer('CONO3E');
                $table->string('CLCD3E');
                $table->string('SCCD3E');
                $table->string('SCDS3E');
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
        Schema::dropIfExists('PRSCMS0');
    }
}
