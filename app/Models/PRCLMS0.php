<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PRCLMS0 extends BaseModel
{
    public $table = 'PRCLMS0';

    protected $fillable = ['CONO3D','CLCD3D','CLDS3D'];

    public function COMPMS0()
    {
        return $this->belongsTo('App\Models\COMPMS0');
    }
}
