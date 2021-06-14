<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePRDAVL0STable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('PRDAVL0')) {
            Schema::create('PRDAVL0', function (Blueprint $table) {
                $table->integer('CONO5V');
                $table->string('PRCD5V');
                $table->string('CRCD5V');
                $table->integer('TLOH5V');
                $table->integer('OH015V');
                $table->integer('OH025V');
                $table->integer('OH035V');
                $table->integer('OH045V');
                $table->integer('OH055V');
                $table->integer('OH065V');
                $table->integer('OH075V');
                $table->integer('OH085V');
                $table->integer('OH095V');
                $table->integer('OH105V');
                $table->integer('OH115V');
                $table->integer('OH125V');
                $table->integer('TLUA5V');
                $table->integer('UA015V');
                $table->integer('UA025V');
                $table->integer('UA035V');
                $table->integer('UA045V');
                $table->integer('UA055V');
                $table->integer('UA065V');
                $table->integer('UA075V');
                $table->integer('UA085V');
                $table->integer('UA095V');
                $table->integer('UA105V');
                $table->integer('UA115V');
                $table->integer('UA125V');
                $table->integer('TLNH5V');
                $table->integer('NH015V');
                $table->integer('NH025V');
                $table->integer('NH035V');
                $table->integer('NH045V');
                $table->integer('NH055V');
                $table->integer('NH065V');
                $table->integer('NH075V');
                $table->integer('NH085V');
                $table->integer('NH095V');
                $table->integer('NH105V');
                $table->integer('NH115V');
                $table->integer('NH125V');
                $table->integer('TLHL5V');
                $table->integer('HL015V');
                $table->integer('HL025V');
                $table->integer('HL035V');
                $table->integer('HL045V');
                $table->integer('HL055V');
                $table->integer('HL065V');
                $table->integer('HL075V');
                $table->integer('HL085V');
                $table->integer('HL095V');
                $table->integer('HL105V');
                $table->integer('HL115V');
                $table->integer('HL125V');
                $table->integer('TLWP5V');
                $table->integer('WP015V');
                $table->integer('WP025V');
                $table->integer('WP035V');
                $table->integer('WP045V');
                $table->integer('WP055V');
                $table->integer('WP065V');
                $table->integer('WP075V');
                $table->integer('WP085V');
                $table->integer('WP095V');
                $table->integer('WP105V');
                $table->integer('WP115V');
                $table->integer('WP125V');
                $table->integer('TLPO5V');
                $table->integer('PO015V');
                $table->integer('PO025V');
                $table->integer('PO035V');
                $table->integer('PO045V');
                $table->integer('PO055V');
                $table->integer('PO065V');
                $table->integer('PO075V');
                $table->integer('PO085V');
                $table->integer('PO095V');
                $table->integer('PO105V');
                $table->integer('PO115V');
                $table->integer('PO125V');
                $table->integer('TLAV5V');
                $table->integer('AV015V');
                $table->integer('AV025V');
                $table->integer('AV035V');
                $table->integer('AV045V');
                $table->integer('AV055V');
                $table->integer('AV065V');
                $table->integer('AV075V');
                $table->integer('AV085V');
                $table->integer('AV095V');
                $table->integer('AV105V');
                $table->integer('AV115V');
                $table->integer('AV125V');
                $table->integer('AVCS5V');
                $table->integer('SSAC5V');
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
        Schema::dropIfExists('PRDAVL0');
    }
}