<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Builder as Builder;
use Illuminate\Database\Connection as Connection;


class UPCXRF0 extends BaseModel
{
    public $table = 'UPCXRF0';
    public $companyColumn = "CONO5R";

    protected $guarded = [];

}
