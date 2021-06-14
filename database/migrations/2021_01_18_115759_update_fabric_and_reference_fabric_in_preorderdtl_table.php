<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateFabricAndReferenceFabricInPreorderdtlTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('PreOrderDtl', function (Blueprint $table) {
            if (Schema::hasTable('PreOrderDtl')) {
                Schema::table('PreOrderDtl', function (Blueprint $table) {
                    if (Schema::hasColumn('PreOrderDtl', 'Fabric')) {
                        $table->string('Fabric')->change();
                    }
                    if (Schema::hasColumn('PreOrderDtl', 'ReferenceFabric')) {
                        $table->string('ReferenceFabric')->change();
                    }
                });
              }
        });
    }

}
