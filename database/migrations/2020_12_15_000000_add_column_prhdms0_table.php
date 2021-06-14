<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnPRHDMS0Table extends Migration
{
    public function up()
    {
        if (Schema::hasTable('PRHDMS0')) {
            Schema::table('PRHDMS0', function (Blueprint $table) {
                if (!Schema::hasColumn('PRHDMS0', 'SZCD3K')) {
                    $table->string('SZCD3K')->nullable();
                }
                if (!Schema::hasColumn('PRHDMS0', 'RTPR3K')) {
                    $table->decimal('RTPR3K',7,2)->nullable();
                }
            });
          }
    }
}