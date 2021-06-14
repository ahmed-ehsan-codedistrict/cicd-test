<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CSSRMS0 extends Model
{
    public $table = 'CSSRMS0';

    protected $fillable = ['CompanyNo','CustomerNo','SRCDW8','SRDSW8'];

    public function Customer()
    {
        return $this->belongsTo(CUSTMS0::class,'CustomerNo', 'CustomerNo');
    }
}
