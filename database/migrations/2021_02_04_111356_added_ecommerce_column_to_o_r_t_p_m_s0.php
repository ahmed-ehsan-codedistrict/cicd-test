<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddedEcommerceColumnToORTPMS0 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ORTPMS0', function (Blueprint $table) {
            if (Schema::hasTable('ORTPMS0')) {
                Schema::table('ORTPMS0', function (Blueprint $table) {
                    if (!Schema::hasColumn('ORTPMS0', 'Ecommerce')) {
                        $table->string('Ecommerce',1)->nullable();
                    }
                });
              }
        });
    }
}
