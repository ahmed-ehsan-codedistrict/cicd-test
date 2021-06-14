<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Builder as Builder;
use Illuminate\Database\Connection as Connection;
use App\Scopes\TenantScope;

class ProdPLM extends BaseModel
{
    public $table = 'ProdPLM';
    public $companyColumn = "CompanyNo";
    public  $primaryKey = "Brand";

    protected $fillable = [
        'CompanyNo', 'Style', 'Market',
        'Season', 'Brand', 'Designer',
        'FabType', 'FabricName', 'Description',
        'FabricContent'
    ];

    public function COMPMS0()
    {
        return $this->belongsTo('App\Models\COMPMS0');
    }

    public function PRHDMS0()
    {
        return $this->belongsTo('App\Models\PRHDMS0');
    }

    public function Users()
    {
        return $this->belongsToMany('App\User', 'UserBrand', 'Brand', 'UserId');
    }


    //get Distinct Brands
    public static function getDistinctBrands()
    {
        try {
           return $Brands = ProdPLM::withoutGlobalScope(new TenantScope())->distinct("ProdPLM.Brand")
                ->select(
                    "ProdPLM.Brand",
                    "ProdPLM.CompanyNo"
                )
                ->leftJoin("Brands", function ($join) {
                    $join->on('Brand', '=', 'Name');
                    $join->on('ProdPLM.CompanyNo', '=', 'Brands.CompanyNo');
                })
                ->whereNull("Name")
                ->where("Brand", "!=", '')
                ->get();

        } catch (\Throwable $th) {
            return $th->getMessage();
        }
    }
}
