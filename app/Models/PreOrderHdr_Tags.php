<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PreOrderHdr_Tags extends BaseModel
{
    public $table = 'PreOrderHdr_Tags';
    public $primaryKey = 'id';
    public $timestamps = true;
    protected $fillable = ['CompanyNo', 'PreOrderNum', 'TagId', 'UserId'];
}
