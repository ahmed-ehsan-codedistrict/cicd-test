<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UserBrandTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('UserBrand')) {
            Schema::create('UserBrand', function (Blueprint $table) {
                $table->integer('CompanyNo');
                $table->integer('UserId');
                $table->string('Brand');
                $table->primary(['CompanyNo','UserId','Brand']);
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
        Schema::dropIfExists('UserBrand');
    }
}
