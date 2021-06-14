<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLinesheetIdColumToLinesheetGroupProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('LineSheetGroupProducts')) {
            Schema::table('LineSheetGroupProducts', function (Blueprint $table) {
                $table->integer("LinesheetId")->default(0);
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
        if (Schema::hasColumn('LineSheetGroupProducts', 'LinesheetId')) {

            Schema::table('LineSheetGroupProducts', function (Blueprint $table) {
                $table->dropColumn('LinesheetId');
            });
        }
    }
}
