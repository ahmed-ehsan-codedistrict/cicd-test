<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePreOrderDtlsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('PreOrderDtl')) {
            Schema::create('PreOrderDtl', function (Blueprint $table) {
                $table->integer('CompanyNo');
                $table->integer('PreOrderNumdtl');
                $table->integer('PreOrderLinenum');
                $table->string('Style');
                $table->string('Color');
                $table->string('Fabric');
                $table->string('ReferenceStyle');
                $table->string('ReferenceColor');
                $table->string('ReferenceFabric');
                $table->string('Instructions1');
                $table->string('Instructions2');
                $table->string('Instructions3');
                $table->string('Instructions4');
                $table->string('RefStyleInstructions1');
                $table->string('RefStyleInstructions2');
                $table->string('RefStyleInstructions3');
                $table->string('RefStyleInstructions4');
                $table->string('SizeCode');
                $table->integer('Scale1')->nullable();
                $table->integer('Scale2')->nullable();
                $table->integer('Scale3')->nullable();
                $table->integer('Scale4')->nullable();
                $table->integer('Scale5')->nullable();
                $table->integer('Scale6')->nullable();
                $table->integer('Scale7')->nullable();
                $table->integer('Scale8')->nullable();
                $table->integer('Scale9')->nullable();
                $table->integer('Scale10')->nullable();
                $table->integer('Scale11')->nullable();
                $table->integer('Scale12')->nullable();
                $table->integer('Qty')->nullable();
                $table->decimal('Price',19,4)->nullable();
                $table->string('ActionStatus')->nullable();
                $table->string('CutAll')->nullable();
                $table->string('SampleNeeded')->nullable();
                $table->date('DateCreated')->nullable();
                $table->string('UserCreated')->nullable();
                $table->date('DateMaintained')->nullable();
                $table->string('UserMaintained')->nullable();
                $table->string('DtlTransferToGDTS')->nullable();
                $table->decimal('Retail',9,2)->nullable();
                $table->decimal('Ext',12,2)->nullable();
                $table->string('CutMinimum')->nullable();
                $table->string('LineSample')->nullable();
                $table->string('ProdType')->nullable();
                $table->integer('CutQty')->nullable();
                $table->string('OrderType')->nullable();
                $table->string('CutInstruction')->nullable();
                $table->string('NoSizeRatio')->nullable();
                $table->date('AdSampleDate')->nullable();
                $table->integer('AdQty')->nullable();
                $table->date('LineSampleDate')->nullable();
                $table->integer('LineQty')->nullable();
                $table->integer('Divno')->nullable();
                $table->integer('MarginDeptQty')->nullable();
                $table->decimal('MarginDeptPrice',6,2)->nullable();
                $table->integer('MarginDeptDscQty')->nullable();
                $table->decimal('MarginDeptDscPrice',6,2)->nullable();
                $table->integer('MarginSpecialtyQty')->nullable();
                $table->decimal('MarginSpecialtyPrice',6,2)->nullable();
                $table->string('ReOrder')->nullable();
                $table->string('OrdMarginFlg')->nullable();
                $table->decimal('MSRP',6,2)->nullable();
                $table->string('SalesDescrip')->nullable();
                $table->string('CadReq')->nullable();
                $table->string('SizeRatio')->nullable();
                $table->string('TOPSample')->nullable();
                $table->date('TOPDate')->nullable();
                $table->integer('TOPQty')->nullable();
                $table->string('AdSizeCmt')->nullable();
                $table->string('LineSizeCmt')->nullable();
                $table->string('TOPSizeCmt')->nullable();
                $table->decimal('Sale',6,2)->nullable();
                $table->string('ExclusiveStyle')->nullable();
                $table->string('PPSample')->nullable();
                $table->date('PPDate')->nullable();
                $table->integer('PPQty')->nullable();
                $table->string('PPSizeCmt')->nullable();
                $table->string('CutHold')->nullable();
                $table->date('CutHoldDate')->nullable();
                $table->string('BuyType')->nullable();
                $table->decimal('Ad01',5,0)->nullable();
                $table->decimal('Ad02',5,0)->nullable();
                $table->decimal('Ad03',5,0)->nullable();
                $table->decimal('Ad04',5,0)->nullable();
                $table->decimal('Ad05',5,0)->nullable();
                $table->decimal('Ad06',5,0)->nullable();
                $table->decimal('Ad07',5,0)->nullable();
                $table->decimal('Ad08',5,0)->nullable();
                $table->decimal('Ad09',5,0)->nullable();
                $table->decimal('Ad10',5,0)->nullable();
                $table->decimal('Ad11',5,0)->nullable();
                $table->decimal('Ad12',5,0)->nullable();
                $table->decimal('Line01',5,0)->nullable();
                $table->decimal('Line02',5,0)->nullable();
                $table->decimal('Line03',5,0)->nullable();
                $table->decimal('Line04',5,0)->nullable();
                $table->decimal('Line05',5,0)->nullable();
                $table->decimal('Line06',5,0)->nullable();
                $table->decimal('Line07',5,0)->nullable();
                $table->decimal('Line08',5,0)->nullable();
                $table->decimal('Line09',5,0)->nullable();
                $table->decimal('Line10',5,0)->nullable();
                $table->decimal('Line11',5,0)->nullable();
                $table->decimal('Line12',5,0)->nullable();
                $table->decimal('TOP01',5,0)->nullable();
                $table->decimal('TOP02',5,0)->nullable();
                $table->decimal('TOP03',5,0)->nullable();
                $table->decimal('TOP04',5,0)->nullable();
                $table->decimal('TOP05',5,0)->nullable();
                $table->decimal('TOP06',5,0)->nullable();
                $table->decimal('TOP07',5,0)->nullable();
                $table->decimal('TOP08',5,0)->nullable();
                $table->decimal('TOP09',5,0)->nullable();
                $table->decimal('TOP10',5,0)->nullable();
                $table->decimal('TOP11',5,0)->nullable();
                $table->decimal('TOP12',5,0)->nullable();
                $table->primary(['CompanyNo','PreOrderNumdtl','PreOrderLinenum']);
                //$table->timestamps();
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
        Schema::dropIfExists('PreOrderDtl');
    }
}