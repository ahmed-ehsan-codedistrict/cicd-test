<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PreOrderDtl extends BaseModel
{
    public $table = 'PreOrderDtl';

    public $timestamps = false;

    protected $fillable = [
        "PreOrderNumdtl",
        "Style",
        "Color",
        "Qty",
        "Price",
        "Ext",
        "UserCreated",
        "UserMaintained",
        "DateCreated",
        "DateMaintained",
        "CompanyNo",
        "PreOrderLinenum"
    ];



    public static function bulkInsert($recordArr)
    {
        try {
            return  PreOrderDtl::insert(
                $recordArr
            );
        } catch (\Throwable $th) {
            return $th->getMessage();
        }
    }
}
