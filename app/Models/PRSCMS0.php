<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PRSCMS0 extends BaseModel
{
    public $table = 'PRSCMS0';

    protected $fillable = ['CONO3E','CLCD3E','SCCD3E','SCDS3E'];

    public function COMPMS0()
    {
        return $this->belongsTo('App\Models\COMPMS0');
    }
}
