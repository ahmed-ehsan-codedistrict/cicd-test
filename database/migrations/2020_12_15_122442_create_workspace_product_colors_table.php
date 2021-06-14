<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWorkspaceProductColorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('WorkspaceColors')) {
            Schema::create('WorkspaceColors', function (Blueprint $table) {
                $table->integer('WorkspaceId');
                $table->string('ColorId');
                $table->primary(['WorkspaceId','ColorId']);
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
        Schema::dropIfExists('workspace_product_colors');
    }
}
