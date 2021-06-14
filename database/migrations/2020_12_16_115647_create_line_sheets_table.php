<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLineSheetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('LineSheets')) {
            Schema::create('LineSheets', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->integer('CompanyNo');
                $table->string('lineSheetName');
                $table->integer('customerId');
                $table->integer('templateId');
                $table->text('bannerPath')->nullable();
                $table->string('brand')->nullable();
                $table->date('startDate')->nullable();
                $table->date('endDate')->nullable();
                $table->tinyInteger('visibility')->default(1);  // 1 for Private, 0 for Public
                $table->tinyInteger('status')->default(1);      // 1 for active , 0 for Inactive
                $table->tinyInteger('isArchived')->default(1);  // 1 for Not Archived, 0 for archived
                $table->integer('createdBy');                   // created By consider as UserID
                $table->integer('updatedBy')->nullable();
                $table->timestamps();
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
        Schema::dropIfExists('LineSheets');
    }
}
