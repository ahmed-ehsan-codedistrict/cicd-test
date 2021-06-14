<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCartsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('Cart')) {
            Schema::create('Cart', function (Blueprint $table) {
                $table->bigIncrements('Id');
                $table->string('UserId');
                $table->string('ProductId');
                $table->string('ColorId');
                $table->integer('Quantity');
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
        Schema::dropIfExists('Cart');
    }
}
