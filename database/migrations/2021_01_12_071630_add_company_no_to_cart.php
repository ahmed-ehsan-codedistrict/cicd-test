<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCompanyNoToCart extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('Cart', function (Blueprint $table) {
            if (Schema::hasTable('Cart')) {
                Schema::table('Cart', function (Blueprint $table) {
                    if (!Schema::hasColumn('Cart', 'CompanyNo')) {
                        $table->integer('CompanyNo')->default(1);
                    }
                });
              }
        });
    }
}
