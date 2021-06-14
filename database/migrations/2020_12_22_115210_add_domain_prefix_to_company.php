<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDomainPrefixToCompany extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('COMPMS0')) {
            Schema::table('COMPMS0', function (Blueprint $table) {
                if (!Schema::hasColumn('COMPMS0', 'DomainPrefix')) {
                    $table->string("DomainPrefix")->unique()->default('');
                }
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
        Schema::table('COMPMS0', function (Blueprint $table) {
             $table->dropColumn('DomainPrefix');
        });
    }
}
