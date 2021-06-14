<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDisplayOrderFieldToLSGroupProductTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('LineSheetGroupProducts', 'DisplayOrder')) {
            Schema::table('LineSheetGroupProducts', function (Blueprint $table) {
                $table->integer('DisplayOrder')->nullable();
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
        if (Schema::hasColumn('LineSheetGroupProducts', 'DisplayOrder')) {
            Schema::table('LineSheetGroupProducts', function (Blueprint $table) {
                $table->dropColumn('DisplayOrder');
            });
        }
    }
}
