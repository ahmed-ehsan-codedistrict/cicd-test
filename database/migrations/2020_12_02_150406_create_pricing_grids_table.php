<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePricingGridsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('PricingGrid')) {
            Schema::create('PricingGrid', function (Blueprint $table) {
                $table->integer('CompanyNo');
                $table->integer('PricingGridID')->unique();
                $table->decimal('code',14,2);
                $table->string('pricing');
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
        Schema::dropIfExists('PricingGrid');
    }
}
