<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUserIdtoSLMNMS0 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('SLMNMS0')) {
            Schema::table('SLMNMS0', function (Blueprint $table) {
                if (!Schema::hasColumn('SLMNMS0', 'UserId')) {
                    $table->integer('UserId')->nullable();
                }
            });
          }
    }
}
