<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSizeRangesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('SizeRange')) {
            Schema::create('SizeRange', function (Blueprint $table) {
                $table->integer('ComapanyNo');
                $table->string('SizeRange');
                $table->string('SZ01');
                $table->string('SZ02');
                $table->string('SZ03');
                $table->string('SZ04');
                $table->string('SZ05');
                $table->string('SZ06');
                $table->string('SZ07');
                $table->string('SZ08');
                $table->string('SZ09');
                $table->string('SZ10');
                $table->string('SZ11');
                $table->string('SZ12');
                $table->primary(['Companyno','SizeRange']);
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
        Schema::dropIfExists('SizeRange');
    }
}