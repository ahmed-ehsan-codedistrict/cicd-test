<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductTagsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('Product_Tags')) {
            Schema::create('Product_Tags', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->integer('CompanyNo');
                $table->string('ProductId',30);
                $table->bigInteger('TagId');
                $table->integer('userId')->nullable()->default(0);
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
        Schema::dropIfExists('_product__tags');
    }
}
