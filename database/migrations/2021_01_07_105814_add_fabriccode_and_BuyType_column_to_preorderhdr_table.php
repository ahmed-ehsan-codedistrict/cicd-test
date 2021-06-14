<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFabricCodeAndBuyTypeColumnToPreorderhdrTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('PreOrderHdr', 'FabricCode')) {
            Schema::table('PreOrderHdr', function (Blueprint $table) {
                $table->string('FabricCode')->nullable();
            });
        }
        if (!Schema::hasColumn('PreOrderHdr', 'BuyType')) {
            Schema::table('PreOrderHdr', function (Blueprint $table) {
                $table->string('BuyType')->nullable();
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
        if (Schema::hasColumn('PreOrderHdr', 'FabricCode')) {
            Schema::table('PreOrderHdr', function (Blueprint $table) {
                $table->dropColumn('FabricCode');
            });
        }
        if (Schema::hasColumn('PreOrderHdr', 'BuyType')) {
            Schema::table('PreOrderHdr', function (Blueprint $table) {
                $table->dropColumn('BuyType');
            });
        }
    }
}
