<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    public $table = 'Brands';

    public $timestamps = true;

    protected $fillable = ['id', 'Name', 'CompanyNo', 'Path'];

}
