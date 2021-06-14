<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DivLogo extends BaseModel
{
    public $table = 'DivLogo';

    public $timestamps = false;

    protected $fillable = ['CompanyNo', 'DivisionNo', 'LogoName', 'LogoFileName'];


    public static function userDivisionLogo($divisions)
    {
         try {
            return DivLogo::distinct("LogoName")
            ->select("LogoName as Value", "LogoFileName as ID")
            ->whereIn("DivisionNo", $divisions)
            ->whereNotNull("LogoName")
            ->get();
         } catch (\Throwable $th) {
             return $th->getMessage();
         }
    }
}
