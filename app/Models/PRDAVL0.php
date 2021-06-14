<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PRDAVL0 extends BaseModel
{
    public $table = 'PRDAVL0';

    protected $fillable = ['CONO5V','PRCD5V','CRCD5V','TLOH5V',
                            'OH015V','OH025V','OH035V','OH045V',
                            'OH055V','OH065V','OH075V','OH085V',
                            'OH095V','OH105V','OH115V','OH125V',
                            'TLUA5V','UA015V','UA025V','UA035V',
                            'UA045V','UA055V','UA065V','UA075V',
                            'UA085V','UA095V','UA105V','UA115V',
                            'UA125V','TLNH5V','NH015V','NH025V',
                            'NH035V','NH045V','NH055V','NH065V',
                            'NH075V','NH085V','NH095V','NH105V',
                            'NH115V','NH125V','TLHL5V','HL015V',
                            'HL025V','HL035V','HL045V','HL055V',
                            'HL065V','HL075V','HL085V','HL095V',
                            'HL105V','HL115V','HL125V','TLWP5V',
                            'WP015V','WP025V','WP035V','WP045V',
                            'WP055V','WP065V','WP075V','WP085V',
                            'WP095V','WP105V','WP115V','WP125V',
                            'TLPO5V','PO015V','PO025V','PO035V',
                            'PO045V','PO055V','PO065V','PO075V',
                            'PO085V','PO095V','PO105V','PO115V',
                            'PO125V','TLAV5V','AV015V','AV025V',
                            'AV035V','AV045V','AV055V','AV065V',
                            'AV075V','AV085V','AV095V','AV105V',
                            'AV115V','AV125V','AVCS5V','SSAC5V'];
}