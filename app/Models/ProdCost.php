<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProdCost extends BaseModel
{
    public $table = 'ProdCost';

    public $primaryKey = 'Style';

    public $incrementing = false;

    protected $guarded = [];

    public function product()
    {
        return $this->belongsTo('App\Models\PRHDMS0', 'Style', 'Style');
    }
}
