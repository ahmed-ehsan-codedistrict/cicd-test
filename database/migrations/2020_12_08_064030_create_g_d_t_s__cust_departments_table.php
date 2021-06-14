<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGDTSCustDepartmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('GDTS_CustDepartment')) {
            Schema::create('GDTS_CustDepartment', function (Blueprint $table) {
                $table->integer('Companyno');
                $table->integer('Custno');
                $table->string('Department');
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
        Schema::dropIfExists('GDTS_CustDepartment');
    }
}