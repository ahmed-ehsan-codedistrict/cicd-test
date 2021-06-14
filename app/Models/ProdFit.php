<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProdFit extends BaseModel
{
    public $table = 'ProdFit';

    protected $fillable = ['CompanyNo','Style','id_attribute','attribname','attribval'];

    public function COMPMS0()
    {
        return $this->belongsTo('App\Models\COMPMS0');
    }

    public function PRHDMS0()
    {
        return $this->belongsTo('App\Models\PRHDMS0');
    }
}

