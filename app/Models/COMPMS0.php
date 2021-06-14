<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Scopes\TenantScope;

class COMPMS0 extends Model
{

    public $table = 'COMPMS0';

    public $primaryKey = "CompanyNo";

    public $timestamps = false;

    protected $fillable = ['CompanyNo', 'CONO2C', 'DomainPrefix'];

    public function users()
    {
        return $this->hasMany('App\User', 'CompanyNo', 'CompanyNo');
    }

    public function SLMNMS0()
    {
        return $this->hasMany('App\Models\SLMNMS0', 'CONO2H', 'CONO2C');
    }

    public function COLRMS0()
    {
        return $this->hasMany('App\Models\COLRMS0', 'CONO3J', 'CONO2C');
    }

    public function MKGPMS0()
    {
        return $this->hasMany('App\Models\MKGPMS0', 'CONO3N', 'CONO2C');
    }

    public function PRCLMS0()
    {
        return $this->hasMany('App\Models\PRCLMS0', 'CONO3D', 'CONO2C');
    }

    public function PRSCMS0()
    {
        return $this->hasMany('App\Models\PRSCMS0', 'CONO3E', 'CONO2C');
    }

    public function CUSTMS0()
    {
        return $this->hasMany('App\Models\CUSTMS0', 'CONO2S', 'CONO2C');
    }

    public function PRHDMS0()
    {
        return $this->hasMany('App\Models\PRHDMS0', 'CONO3K', 'CONO2C');
    }

    public function PRDTMS0()
    {
        return $this->hasMany('App\Models\PRDTMS0', 'CONO3L', 'CONO2C');
    }

    public function PricingGrid()
    {
        return $this->hasMany('App\Models\PricingGrid', 'CompanyNo', 'CONO2C');
    }

    public function ProdPLM()
    {
        return $this->hasMany('App\Models\ProdPLM', 'CompanyNo', 'CONO2C');
    }

    public function ProdFit()
    {
        return $this->hasMany('App\Models\ProdFit', 'CompanyNo', 'CONO2C');
    }

    public function StyleAvail()
    {
        return $this->hasMany('App\Models\StyleAvail', 'CompanyNo', 'CONO2C');
    }

    public function LineSheetHdr()
    {
        return $this->hasMany('App\Models\LineSheetHdr', 'CompanyNo', 'CONO2C');
    }

    public function LineSheetDtl()
    {
        return $this->hasMany('App\Models\LineSheetDtl', 'CompanyNo', 'CONO2C');
    }
}
