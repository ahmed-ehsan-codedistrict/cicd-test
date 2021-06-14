<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePreOrderHdrsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('PreOrderHdr')) {
            Schema::create('PreOrderHdr', function (Blueprint $table) {
                $table->integer('CompanyNo');
                $table->integer('PreOrderNum');
                $table->string('PreOrderStatus')->nullable();
                $table->string('CustAcct')->nullable();
                $table->integer('CancelDate')->nullable();
                $table->string('PreOrderType')->nullable();
                $table->string('Buyer')->nullable();
                $table->string('Lbl')->nullable();
                $table->string('SizeRange')->nullable();
                $table->string('Grp')->nullable();
                $table->string('Salesperson')->nullable();
                $table->string('ProdType')->nullable();
                $table->string('Transferred')->nullable();
                $table->integer('SwatPOAssigned')->nullable();
                $table->integer('SwatPOAssigned2')->nullable();
                $table->integer('SwatPOAssigned3')->nullable();
                $table->integer('SwatPOAssigned4')->nullable();
                $table->integer('SwatPOAssigned5')->nullable();
                $table->string('Division')->nullable();
                $table->date('DateCreated')->nullable();
                $table->string('UserCreated')->nullable();
                $table->date('DateMaintained')->nullable();
                $table->string('UserMaintained')->nullable();
                $table->date('DraftDate')->nullable();
                $table->date('CustomerSvcDate')->nullable();
                $table->integer('PrintDesign')->nullable();
                $table->integer('PrintProd')->nullable();
                $table->date('PrintedDate')->nullable();
                $table->integer('EmailCustSvc')->nullable();
                $table->date('InStoreDate')->nullable();
                $table->string('Region')->nullable();
                $table->integer('TotalExt')->nullable();
                $table->integer('TotalUnits')->nullable();
                $table->string('CustomerRef')->nullable();
                $table->date('StartDate')->nullable();
                $table->integer('LogNo')->nullable();
                $table->integer('CustNo')->nullable();
                $table->integer('PayType')->nullable();
                $table->date('BOMDate')->nullable();
                $table->date('FinalDate')->nullable();
                $table->date('Shipto')->nullable();
                $table->string('ShipAddress')->nullable();
                $table->string('CCType')->nullable();
                $table->string('OMQueue')->nullable();
                $table->date('OrderMarginDate')->nullable();
                $table->string('UserAssign')->nullable();
                $table->integer('OrdertoAS400')->nullable();
                $table->integer('TOPHold')->nullable();
                $table->integer('FitAprv')->nullable();
                $table->string('Season')->nullable();
                $table->integer('EcommCust')->nullable();
                $table->string('OrdTyp')->nullable();
                $table->string('Description')->nullable();
                $table->string('CustDept')->nullable();
                $table->string('UnconfirmScale')->nullable();
                $table->integer('NordRack')->nullable();
                $table->primary(['CompanyNo','PreOrderNum']);
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
        Schema::dropIfExists('PreOrderHdr');
    }
}
