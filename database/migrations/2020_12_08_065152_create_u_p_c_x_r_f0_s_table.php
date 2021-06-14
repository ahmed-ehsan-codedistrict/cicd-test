<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUPCXRF0STable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('UPCXRF0')) {
            Schema::create('UPCXRF0', function (Blueprint $table) {
                $table->integer('CONO5R');
                $table->string('PRCD5R');
                $table->string('CRCD5R');
                $table->string('SZCD5R');
                $table->integer('UPCN5R');
                $table->integer('DVNO5R');
                $table->integer('SLCD5R');
                $table->integer('NCLR5R');
                $table->string('CRDS5R');
                $table->integer('NSIZ5R');
                $table->string('SZDS5R');
                $table->string('SNST5R');
                $table->string('CATA5R');
                $table->integer('CRDT5R');
                $table->integer('UPSQ5R');
                $table->string('ETP15R');
                $table->string('MBN15R');
                $table->integer('MBD15R');
                $table->integer('MBT15R');
                $table->string('MBS15R');
                $table->string('FAS15R');
                $table->integer('FAD15R');
                $table->string('ISA15R');
                $table->string('NTW15R');
                $table->string('MBC15R');
                $table->integer('NOM15R');
                $table->integer('MSD15R');
                $table->integer('MST15R');
                $table->string('ETP25R');
                $table->string('MBN25R');
                $table->integer('MBD25R');
                $table->integer('MBT25R');
                $table->string('MBS25R');
                $table->string('FAS25R');
                $table->integer('FAD25R');
                $table->string('ISA25R');
                $table->string('NTW25R');
                $table->string('MBC25R');
                $table->integer('NOM25R');
                $table->integer('MSD25R');
                $table->integer('MST25R');
                $table->integer('MLMT5R');
                $table->integer('MLD25R');
                $table->integer('MLT25R');
                $table->integer('PPCD5R');
                $table->integer('PPQT5R');
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
        Schema::dropIfExists('UPCXRF0');
    }
}
