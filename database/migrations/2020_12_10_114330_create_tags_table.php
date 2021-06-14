<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTagsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('Tags')) {
            Schema::create('Tags', function (Blueprint $table) {
                $table->bigIncrements('TagId');
                $table->integer('CompanyNo');
                $table->string('TagName', 100);
                $table->timestamps();
            });

            //rename the table name
            Schema::rename('tags', 'Tags');
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tags');
    }
}
