<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Model;

class MKGPMS0 extends BaseModel
{
    public $table = 'MKGPMS0';

    protected $fillable = ['CONO3N','MKGP3N','MKDS3N'];

    public function COMPMS0()
    {
        return $this->belongsTo('App\Models\COMPMS0');
    }
}
