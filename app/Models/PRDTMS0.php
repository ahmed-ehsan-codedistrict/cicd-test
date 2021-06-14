<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PRDTMS0 extends BaseModel
{
    public $table = 'PRDTMS0';

    protected $fillable = ['CONO3L','PRCD3L','CRCD3L'];

    public function COLRMS0()
    {
        return $this->belongsToMany('App\Models\COLRMS0','PRDTMS0_COLRMS0','Color','Color');
    }

    public function PRHDMS0()
    {
        return $this->belongsTo('App\Models\PRHDMS0');
    }

    public function COMPMS0()
    {
        return $this->belongsTo('App\Models\COMPMS0');
    }

    public static function getColors($ProductId)
    {
        $results = PRDTMS0::distinct('Style')
                            ->select(DB::raw("TRIM(Style) as ID"),DB::raw("Color as value"))
                            ->where('Style', $ProductId)
                            ->get();
        return $results;
    }
}
