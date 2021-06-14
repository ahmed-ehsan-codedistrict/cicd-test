<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PricingGrid extends Model
{
    protected $table = 'PricingGrid';

    protected $fillable = ['CompanyNo','PricingGridID','code','pricing'];

    public function COMPMS0()
    {
        return $this->belongsTo('App\Models\COMPMS0');
    }
}
