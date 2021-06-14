<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCompanyNoToWorkspace extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
            if (Schema::hasTable('Workspaces')) {
                Schema::table('Workspaces', function (Blueprint $table) {
                    if (!Schema::hasColumn('Workspaces', 'CompanyNo')) {
                        $table->integer('CompanyNo');
                    }
                });
              }
    }
}
