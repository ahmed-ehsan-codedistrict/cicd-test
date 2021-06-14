<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDescriptionColumnToPreorderdtlTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('PreOrderDtl', function (Blueprint $table) {
            if (Schema::hasTable('PreOrderDtl')) {
                Schema::table('PreOrderDtl', function (Blueprint $table) {
                    if (!Schema::hasColumn('PreOrderDtl', 'Description')) {
                        $table->text('Description')->nullable();
                    }
                });
              }
        });
    }
}
