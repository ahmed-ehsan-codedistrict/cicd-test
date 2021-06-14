<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePreOrderHdrTagsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('PreOrderHdr_Tags')) {
            Schema::create('PreOrderHdr_Tags', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->integer('CompanyNo');
                $table->integer('PreOrderNum');
                $table->integer('TagId');
                $table->integer('UserId')->nullable();
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
        Schema::dropIfExists('PreOrderHdr_Tags');
    }
}
