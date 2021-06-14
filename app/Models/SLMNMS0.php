<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SLMNMS0 extends BaseModel
{
    public $table = 'SLMNMS0';

    protected $fillable = ['CONO2H','SMNO2H','FLNM2H','SHNM2H','SMCD2H','userId'];

    public function COMPMS0()
    {
        return $this->belongsTo('App\Models\COMPMS0');
    }

    public function user()
    {
        return  $this->hasOne('App\User');
    }

}
