<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GDTS_NewOrderType extends Model
{
    protected $table = 'GDTS_NewOrderType';

    protected $fillable = ['Companyno','OrderType'];
}

