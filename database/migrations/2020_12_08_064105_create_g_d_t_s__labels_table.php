<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGDTSLabelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('GDTS_Labels')) {
            Schema::create('GDTS_Labels', function (Blueprint $table) {
                $table->string('labels');
                $table->integer('CompanyNo');
                $table->date('DateCreated');
                $table->string('UserCreated');
                $table->date('DateMaintained');
                $table->string('UserMaintained');
                $table->primary(['labels','CompanyNo']);
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
        Schema::dropIfExists('GDTS_Labels');
    }
}