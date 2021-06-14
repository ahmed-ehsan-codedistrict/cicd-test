<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnProdPLMTable extends Migration
{
    public function up()
    {
        if (Schema::hasTable('ProdPLM')) {
            Schema::table('ProdPLM', function (Blueprint $table) {
                if (!Schema::hasColumn('ProdPLM', 'Description')) {
                    $table->string('Description')->nullable();
                }
                if (!Schema::hasColumn('ProdPLM', 'FabricContent')) {
                    $table->string('FabricContent')->nullable();
                }
            });
          }
    }
}