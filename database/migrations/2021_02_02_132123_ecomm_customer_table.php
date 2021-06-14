<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class EcommCustomerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('EcommCustomer')) {
            Schema::create('EcommCustomer', function (Blueprint $table) {
                $table->decimal('CompanyNo',2,0);
                $table->decimal('Custno',7,0);
                $table->string('EcommName',20);
                $table->primary(['CompanyNo','Custno'],'PK_EcommCustomer');
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
        Schema::dropIfExists('EcommCustomer');
    }
}
