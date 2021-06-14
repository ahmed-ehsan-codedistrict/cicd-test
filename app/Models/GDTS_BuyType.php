<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Model;

class GDTS_BuyType extends BaseModel
{
    public $table = 'GDTS_BuyType';

    protected $fillable = ['CompanyNo','Custno','BuyType','BuyTypeDesc'];
}
