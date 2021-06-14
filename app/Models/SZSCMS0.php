<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\PRHDMS0;

class SZSCMS0 extends BaseModel
{
    public $table = 'SZSCMS0';

    public $primaryKey = 'SZCD3G';

    protected $guarded = [];

    public function Product()
    {
        return $this->belongsTo(PRHDMS0::class, 'SZCD3K', 'SZCD3G');
    }
}

