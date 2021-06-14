<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddCompanyNoToWorkspaceColors extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('WorkspaceColors')) {
            Schema::table('WorkspaceColors', function (Blueprint $table) {
                if (!Schema::hasColumn('WorkspaceColors', 'CompanyNo')) {
                    //truncating table
                    DB::table('WorkspaceColors')->truncate();
                    $table->integer('CompanyNo');
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
        Schema::table('workspace_colors', function (Blueprint $table) {
            //
        });
    }
}
