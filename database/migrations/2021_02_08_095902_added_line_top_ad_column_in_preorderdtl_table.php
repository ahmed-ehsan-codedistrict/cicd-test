<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddedLineTopAdColumnInPreorderdtlTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('PreOrderDtl', 'Ad01')){
            Schema::table('PreOrderDtl', function (Blueprint $table) {
                $table->decimal('Ad01',5,0)->nullable();
            });
        }
        if (!Schema::hasColumn('PreOrderDtl', 'Ad02')){
            Schema::table('PreOrderDtl', function (Blueprint $table) {
                $table->decimal('Ad02',5,0)->nullable();
            });
        }
        if (!Schema::hasColumn('PreOrderDtl', 'Ad03')){
            Schema::table('PreOrderDtl', function (Blueprint $table) {
                $table->decimal('Ad03',5,0)->nullable();
            });
        }
        if (!Schema::hasColumn('PreOrderDtl', 'Ad04')){
            Schema::table('PreOrderDtl', function (Blueprint $table) {
                $table->decimal('Ad04',5,0)->nullable();
            });
        }
        if (!Schema::hasColumn('PreOrderDtl', 'Ad05')){
            Schema::table('PreOrderDtl', function (Blueprint $table) {
                $table->decimal('Ad05',5,0)->nullable();
            });
        }
        if (!Schema::hasColumn('PreOrderDtl', 'Ad06')){
            Schema::table('PreOrderDtl', function (Blueprint $table) {
                $table->decimal('Ad06',5,0)->nullable();
            });
        }
        if (!Schema::hasColumn('PreOrderDtl', 'Ad07')){
            Schema::table('PreOrderDtl', function (Blueprint $table) {
                $table->decimal('Ad07',5,0)->nullable();
            });
        }
        if (!Schema::hasColumn('PreOrderDtl', 'Ad08')){
            Schema::table('PreOrderDtl', function (Blueprint $table) {
                $table->decimal('Ad08',5,0)->nullable();
            });
        }
        if (!Schema::hasColumn('PreOrderDtl', 'Ad09')){
            Schema::table('PreOrderDtl', function (Blueprint $table) {
                $table->decimal('Ad09',5,0)->nullable();
            });
        }
        if (!Schema::hasColumn('PreOrderDtl', 'Ad10')){
            Schema::table('PreOrderDtl', function (Blueprint $table) {
                $table->decimal('Ad10',5,0)->nullable();
            });
        }
        if (!Schema::hasColumn('PreOrderDtl', 'Ad11')){
            Schema::table('PreOrderDtl', function (Blueprint $table) {
                $table->decimal('Ad11',5,0)->nullable();
            });
        }
        if (!Schema::hasColumn('PreOrderDtl', 'Ad12')){
            Schema::table('PreOrderDtl', function (Blueprint $table) {
                $table->decimal('Ad12',5,0)->nullable();
            });
        }
        if (!Schema::hasColumn('PreOrderDtl', 'Line01')){
            Schema::table('PreOrderDtl', function (Blueprint $table) {
                $table->decimal('Line01',5,0)->nullable();
            });
        }
        if (!Schema::hasColumn('PreOrderDtl', 'Line02')){
            Schema::table('PreOrderDtl', function (Blueprint $table) {
                $table->decimal('Line02',5,0)->nullable();
            });
        }
        if (!Schema::hasColumn('PreOrderDtl', 'Line03')){
            Schema::table('PreOrderDtl', function (Blueprint $table) {
                $table->decimal('Line03',5,0)->nullable();
            });
        }
        if (!Schema::hasColumn('PreOrderDtl', 'Line04')){
            Schema::table('PreOrderDtl', function (Blueprint $table) {
                $table->decimal('Line04',5,0)->nullable();
            });
        }
        if (!Schema::hasColumn('PreOrderDtl', 'Line05')){
            Schema::table('PreOrderDtl', function (Blueprint $table) {
                $table->decimal('Line05',5,0)->nullable();
            });
        }
        if (!Schema::hasColumn('PreOrderDtl', 'Line06')){
            Schema::table('PreOrderDtl', function (Blueprint $table) {
                $table->decimal('Line06',5,0)->nullable();
            });
        }
        if (!Schema::hasColumn('PreOrderDtl', 'Line07')){
            Schema::table('PreOrderDtl', function (Blueprint $table) {
                $table->decimal('Line07',5,0)->nullable();
            });
        }
        if (!Schema::hasColumn('PreOrderDtl', 'Line08')){
            Schema::table('PreOrderDtl', function (Blueprint $table) {
                $table->decimal('Line08',5,0)->nullable();
            });
        }
        if (!Schema::hasColumn('PreOrderDtl', 'Line09')){
            Schema::table('PreOrderDtl', function (Blueprint $table) {
                $table->decimal('Line09',5,0)->nullable();
            });
        }
        if (!Schema::hasColumn('PreOrderDtl', 'Line10')){
            Schema::table('PreOrderDtl', function (Blueprint $table) {
                $table->decimal('Line10',5,0)->nullable();
            });
        }
        if (!Schema::hasColumn('PreOrderDtl', 'Line11')){
            Schema::table('PreOrderDtl', function (Blueprint $table) {
                $table->decimal('Line11',5,0)->nullable();
            });
        }
        if (!Schema::hasColumn('PreOrderDtl', 'Line12')){
            Schema::table('PreOrderDtl', function (Blueprint $table) {
                $table->decimal('Line12',5,0)->nullable();
            });
        }
        if (!Schema::hasColumn('PreOrderDtl', 'TOP01')){
            Schema::table('PreOrderDtl', function (Blueprint $table) {
                $table->decimal('TOP01',5,0)->nullable();
            });
        }
        if (!Schema::hasColumn('PreOrderDtl', 'TOP02')){
            Schema::table('PreOrderDtl', function (Blueprint $table) {
                $table->decimal('TOP02',5,0)->nullable();
            });
        }
        if (!Schema::hasColumn('PreOrderDtl', 'TOP03')){
            Schema::table('PreOrderDtl', function (Blueprint $table) {
                $table->decimal('TOP03',5,0)->nullable();
            });
        }
        if (!Schema::hasColumn('PreOrderDtl', 'TOP04')){
            Schema::table('PreOrderDtl', function (Blueprint $table) {
                $table->decimal('TOP04',5,0)->nullable();
            });
        }
        if (!Schema::hasColumn('PreOrderDtl', 'TOP05')){
            Schema::table('PreOrderDtl', function (Blueprint $table) {
                $table->decimal('TOP05',5,0)->nullable();
            });
        }
        if (!Schema::hasColumn('PreOrderDtl', 'TOP06')){
            Schema::table('PreOrderDtl', function (Blueprint $table) {
                $table->decimal('TOP06',5,0)->nullable();
            });
        }
        if (!Schema::hasColumn('PreOrderDtl', 'TOP07')){
            Schema::table('PreOrderDtl', function (Blueprint $table) {
                $table->decimal('TOP07',5,0)->nullable();
            });
        }
        if (!Schema::hasColumn('PreOrderDtl', 'TOP08')){
            Schema::table('PreOrderDtl', function (Blueprint $table) {
                $table->decimal('TOP08',5,0)->nullable();
            });
        }
        if (!Schema::hasColumn('PreOrderDtl', 'TOP09')){
            Schema::table('PreOrderDtl', function (Blueprint $table) {
                $table->decimal('TOP09',5,0)->nullable();
            });
        }
        if (!Schema::hasColumn('PreOrderDtl', 'TOP10')){
            Schema::table('PreOrderDtl', function (Blueprint $table) {
                $table->decimal('TOP10',5,0)->nullable();
            });
        }
        if (!Schema::hasColumn('PreOrderDtl', 'TOP11')){
            Schema::table('PreOrderDtl', function (Blueprint $table) {
                $table->decimal('TOP11',5,0)->nullable();
            });
        }
        if (!Schema::hasColumn('PreOrderDtl', 'TOP12')){
            Schema::table('PreOrderDtl', function (Blueprint $table) {
                $table->decimal('TOP12',5,0)->nullable();
            });
        }
        if (!Schema::hasColumn('PreOrderDtl', 'AdSample')){
            Schema::table('PreOrderDtl', function (Blueprint $table) {
                $table->string('AdSample',1)->default('N');
            });
        }
    }
}
