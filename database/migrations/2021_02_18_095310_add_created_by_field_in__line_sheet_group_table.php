<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCreatedByFieldInLineSheetGroupTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('LineSheetGroup')) {
            Schema::table('LineSheetGroup', function (Blueprint $table) {
                $table->integer("CreatedBy")->default(0);
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
        if (Schema::hasColumn('LineSheetGroup', 'CreatedBy')) {

            Schema::table('LineSheetGroup', function (Blueprint $table) {
                $table->dropColumn('CreatedBy');
            });
        }
    }
}
