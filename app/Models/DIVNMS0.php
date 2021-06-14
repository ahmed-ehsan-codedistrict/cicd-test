<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DIVNMS0 extends BaseModel
{
    public $table = 'DIVNMS0';

    public $primaryKey = "DivisionNo";

    protected $fillable = ['CONO3C','DVNO3C','DVNM3C','UPNM3C','SBAP3C','UPSL3C','RNNO3C','INLS3C','INOR3C','RPSQ3C','RQCS3C','RQWH3C'];

    public function Users()
    {
        return $this->belongsToMany('App\User','DivisionUser','DivisionNo','UserId');
    }
}
