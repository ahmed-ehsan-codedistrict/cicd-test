<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSZSCMS0STable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('SZSCMS0')) {
            Schema::create('SZSCMS0', function (Blueprint $table) {
                $table->integer('CONO3G');
                $table->string('SZCD3G');
                $table->string('SZDS3G');
                $table->string('SZ013G');
                $table->string('SZ023G');
                $table->string('SZ033G');
                $table->string('SZ043G');
                $table->string('SZ053G');
                $table->string('SZ063G');
                $table->string('SZ073G');
                $table->string('SZ083G');
                $table->string('SZ093G');
                $table->string('SZ103G');
                $table->string('SZ113G');
                $table->string('SZ123G');
                $table->string('ED013G');
                $table->string('ED023G');
                $table->string('ED033G');
                $table->string('ED043G');
                $table->string('ED053G');
                $table->string('ED063G');
                $table->string('ED073G');
                $table->string('ED083G');
                $table->string('ED093G');
                $table->string('ED103G');
                $table->string('ED113G');
                $table->string('ED123G');
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
        Schema::dropIfExists('SZSCMS0');
    }
}