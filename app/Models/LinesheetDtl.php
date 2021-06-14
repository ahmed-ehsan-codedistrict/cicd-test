<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LinesheetDtl extends Model
{
    protected $table = 'LinesheetDtl';

    protected $fillable = ['SEQKEY','CompanyNo','Style','Color','DivisionNo','ListGroup','ListProdGroup','ListPage','Fabric','DDateCreated','DUserCreated','DDateMaintained','DUserMaintained','Price','TargetRetail','Notes','Care','FabricType','FabContent','SortOrder'];

    public function COMPMS0()
    {
        return $this->belongsTo('App\Models\COMPMS0');
    }
}

