<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLinesheetDtlsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('LinesheetDtl')) {
            Schema::create('LinesheetDtl', function (Blueprint $table) {
                $table->string('SEQKEY');
                $table->integer('CompanyNo');
                $table->string('Style')->nullable();
                $table->string('Color')->nullable();
                $table->integer('DivisionNo')->nullable();
                $table->string('ListGroup')->nullable();
                $table->string('ListProdGroup')->nullable();
                $table->integer('ListPage');
                $table->string('Fabric')->nullable();
                $table->date('DDateCreated')->nullable();
                $table->string('DUserCreated')->nullable();
                $table->date('DDateMaintained')->nullable();
                $table->string('DUserMaintained')->nullable();
                $table->string('Price')->nullable();
                $table->string('TargetRetail')->nullable();
                $table->string('Notes')->nullable();
                $table->string('Care')->nullable();
                $table->string('FabricType')->nullable();
                $table->string('FabContent')->nullable();
                $table->integer('SortOrder');
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
        Schema::dropIfExists('LinesheetDtl');
    }
}
