<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWorkspacesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('Workspaces')) {
            Schema::create('Workspaces', function (Blueprint $table) {
                $table->bigIncrements('Id')->unique();
                $table->string('Type');
                $table->string('ProductId');
                $table->integer('UserId');
                $table->integer('CompanyNo');
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
        Schema::dropIfExists('workspaces');
    }
}
