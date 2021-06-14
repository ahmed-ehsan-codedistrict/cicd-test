<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class COLRMS0 extends BaseModel
{
    public $table = 'COLRMS0';
    public $companyColumn = "CONO3J";

    protected $fillable = ['CONO3J','CRCD3J','CRDS3J','CDES3J','NCLR3J'];

    public function PRDTMS0()
    {
        return $this->belongsToMany('App\Models\PRDTMS0','PRDTMS0_COLRMS0','CRCD3J','CRCD3L');
    }

    public function Workspace()
    {
        return $this->belongsToMany('App\Models\Workspaces','WorkspaceColors','ColorId','WorkspaceId');
    }

    public function COMPMS0()
    {
        return $this->belongsTo('App\Models\COMPMS0');
    }

    public function StyleAvail()
    {
        return $this->hasMany('App\Models\StyleAvail','Color','CRCD3J');
    }
}