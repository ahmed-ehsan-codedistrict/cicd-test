<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLinesheetHdrsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('LinesheetHdr')) {
            Schema::create('LinesheetHdr', function (Blueprint $table) {
                $table->primary('SEQKEY');
                $table->integer('CompanyNo');
                $table->integer('CustNo');
                $table->string('Customer');
                $table->string('Region')->nullable();
                $table->string('ListName');
                $table->date('HDateCreated')->nullable();
                $table->string('HUserCreated')->nullable();
                $table->date('HDateMaintained')->nullable();
                $table->string('HUserMaintained')->nullable();
                $table->string('SortOrder')->nullable();
                $table->string('SortList')->nullable();
                $table->string('SortCriteria')->nullable();
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
        Schema::dropIfExists('LinesheetHdr');
    }
}
