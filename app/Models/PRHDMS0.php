<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PRHDMS0 extends BaseModel
{
    public $table = 'PRHDMS0';

    protected $fillable = ['CONO3K','PRCD3K','PRDS3K','SHDS3K','EXDS3K','CLCD3K','SCCD3K','DVNO3K','MKGP3K', 'SZCD3K', 'RTPR3K'];

    public function PRDTMS0()
    {
        return $this->hasMany('App\Models\PRDTMS0','PRCD3L','PRCD3K');
    }

    public function COMPMS0()
    {
        return $this->belongsTo('App\Models\COMPMS0');
    }

    public function StyleAvail()
    {
        return $this->hasMany('App\Models\StyleAvail','Style','PRCD3K');
    }

    public function ProdPLM()
    {
        return $this->hasMany('App\Models\ProdPLM','Style','PRCD3K');
    }

    public function ProdFit()
    {
        return $this->hasMany('App\Models\ProdFit','Style','PRCD3K');
    }
}

